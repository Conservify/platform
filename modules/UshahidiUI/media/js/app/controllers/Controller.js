/**
 * Ushahidi Main Controller
 *
 * @module     Controller
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'backbone', 'marionette', 'controllers/ModalController',
	'views/AppLayout', 'views/HomeLayout',
	'views/HeaderView', 'views/FooterView', 'views/WorkspacePanelView', 'views/SearchBarView', 'views/PostListView',
	'collections/PostCollection','collections/TagCollection','collections/FormCollection'],
	function(App, Backbone, Marionette, ModalController,
		AppLayout, HomeLayout,
		HeaderView, FooterView, WorkspacePanelView, SearchBarView, PostListView,
		PostCollection, TagCollection, FormCollection)
	{
		return Backbone.Marionette.Controller.extend(
		{
			initialize : function()
			{
				this.layout = new AppLayout();
				App.body.show(this.layout);

				var header = new HeaderView();
				header.on('workspace:toggle', function () {
					App.body.$el.toggleClass('active-workspace');
				});

				this.layout.headerRegion.show(header);
				this.layout.footerRegion.show(new FooterView());
				this.layout.workspacePanel.show(new WorkspacePanelView());

				App.Collections = {};
				App.Collections.Posts = new PostCollection();
				App.Collections.Posts.fetch();
				App.Collections.Tags = new TagCollection();
				App.Collections.Tags.fetch();
				App.Collections.Forms = new FormCollection();
				App.Collections.Forms.fetch();

				this.homeLayout = new HomeLayout({
					collection : App.Collections.Posts
				});
				App.vent.trigger('views:change', 'full');

				this.modalController = new ModalController({
					modal : this.layout.modal
				});
			},
			//gets mapped to in AppRouter's appRoutes
			index : function()
			{
				App.vent.trigger('page:change', 'posts');
				App.Collections.Posts.setFilterParams({});
				this.showHomeLayout();
			},
			postsAll : function()
			{
				App.vent.trigger('page:change', 'posts/all');
				App.Collections.Posts.setFilterParams({
					status : 'all'
				});
				this.showHomeLayout();
			},
			postsUnpublished : function()
			{
				App.vent.trigger('page:change', 'posts/unpublished');
				App.Collections.Posts.setFilterParams({
					status : 'draft'
				});
				this.showHomeLayout();
			},
			postsPublished : function()
			{
				this.index();
			},
			viewsFull : function()
			{
				App.vent.trigger('views:change', 'full');
				this.homeLayout.setViews({
					map: true,
					search: true,
					list: true
				});
				this.showHomeLayout();
			},
			viewsList : function()
			{
				App.vent.trigger('views:change', 'list');
				this.homeLayout.setViews({
					map: false,
					search: true,
					list: true
				});
				this.showHomeLayout();
			},
			viewsMap : function()
			{
				App.vent.trigger('views:change', 'map');
				this.homeLayout.setViews({
					map: true,
					search: true,
					list: false
				});
				this.showHomeLayout();
			},
			showHomeLayout : function()
			{
				if (this.layout.mainRegion.currentView instanceof HomeLayout === false)
				{
					this.layout.mainRegion.show(this.homeLayout);
				}
				this.homeLayout.showRegions();
			},
			postDetail : function(id)
			{
				var that = this,
						postDetailLayout,
						model;

				require(['views/PostDetailLayout', 'views/PostDetailView', 'views/RelatedPostsView', 'views/MapView', 'models/PostModel'],
					function(PostDetailLayout, PostDetailView, RelatedPostsView, MapView, PostModel)
				{
					App.vent.trigger('page:change', 'posts/:id');
					// @TODO find a way to reuse post detail views
					postDetailLayout = new PostDetailLayout();
					that.layout.mainRegion.show(postDetailLayout);

					// @todo improve this to avoid double loading of model (and race conditions)
					//model = App.Collections.Posts.get({id : id});
					model = new PostModel({id: id});
					model.fetch().done(function ()
					{
						model.fetchRelations();
					});

					// Make sure we have loaded the form and user before we render the post details
					model.relationsCallback.done(function()
					{
						postDetailLayout.postDetailRegion.show(new PostDetailView({
							model: model
						}));
						postDetailLayout.relatedPostsRegion.show(new RelatedPostsView({
							collection : new PostCollection(App.Collections.Posts.slice(0, 3)) // fake related posts with first 3 from default collection
						}));
					});

					postDetailLayout.mapRegion.show(new MapView({
						className : 'map-view post-details-map-view',
						collapsed : true,
						model : model
					}));
				});
			},
			sets : function ()
			{
				var that = this;
				require(['views/SetsView'], function(SetsView)
				{
					App.vent.trigger('page:change', 'sets');
					that.layout.mainRegion.show(new SetsView());
				});
			},

			setDetail : function(/* id */)
			{
				var that = this;
				require(['views/SetDetailView'], function(SetDetailView)
				{
					App.vent.trigger('page:change', 'sets/:id');
					that.layout.mainRegion.show(new SetDetailView());
				});
			},

			login : function ()
			{
				var that = this;
				require(['views/LoginView', 'text!templates/Header_login.html', 'handlebars'], function(LoginView, workspaceTpl, Handlebars)
				{
					App.vent.trigger('page:change', 'login');
					that.layout.mainRegion.show(new LoginView());
					// @FIXME this will break other controllers, fix this when wiring login properly
					that.layout.headerRegion.show(new HeaderView({
						template: Handlebars.compile(workspaceTpl)
					}));
					that.layout.footerRegion.close();
				});
			},
			register : function ()
			{
				var that = this;
				require(['views/RegisterView', 'text!templates/Header_login.html', 'handlebars'], function(RegisterView, workspaceTpl, Handlebars)
				{
					App.vent.trigger('page:change', 'register');
					that.layout.mainRegion.show(new RegisterView());
					// @FIXME this will break other controllers, fix this when wiring login properly
					that.layout.headerRegion.show(new HeaderView({
						template: Handlebars.compile(workspaceTpl)
					}));
					that.layout.footerRegion.close();
				});
			},
			// Extra postCreate handler to give us a direct URL to posts/create
			postCreate : function ()
			{
				if (typeof this.layout.mainRegion.currentView === 'undefined')
				{
					this.index();
				}

				this.modalController.postCreate();
			}
		});
	});
