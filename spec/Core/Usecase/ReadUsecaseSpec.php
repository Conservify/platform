<?php

namespace spec\Ushahidi\Core\Usecase;

use Ushahidi\Core\Usecase\ReadRepository;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;

use Ushahidi\Core\Tool\Authorizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReadUsecaseSpec extends ObjectBehavior
{
	function let(Authorizer $auth, ReadRepository $repo)
	{
		// usecases are constructed with an array of named tools
		$this->beConstructedWith(compact('auth', 'repo'));
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Ushahidi\Core\Usecase\ReadUsecase');
	}

	function it_fails_when_the_entity_is_not_found($repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id  = 9999;
		$entity->getId()->willReturn(0);

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... or at least it tried to
		$entity->getResource()->shouldBeCalled();
		$this->shouldThrow('Ushahidi\Core\Exception\NotFoundException')->duringInteract($input);
	}

	function it_fails_when_authorization_is_denied($auth, $repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id = 1;
		$entity->getId()->willReturn($input->id);

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'read';

		// ... and if it fails
		$auth->isAllowed($entity, $action)->willReturn(false);

		// ... the exception requests the userid for the message
		$auth->getUserId()->willReturn(1);
		$entity->getResource()->shouldBeCalled();
		$this->shouldThrow('Ushahidi\Core\Exception\AuthorizerException')->duringInteract($input);
	}

	function it_reads_a_record($auth, $repo, Data $input, Entity $entity)
	{
		// (set the entity id)
		$input->id = 1;
		$entity->getId()->willReturn($input->id);

		// it fetches the record
		$repo->get($input->id)->willReturn($entity);

		// ... run the authorization action
		$action = 'read';
		$auth->isAllowed($entity, $action)->willReturn(true);

		// ... finally returning the new record
		$this->interact($input)->shouldReturn($entity);
	}
}
