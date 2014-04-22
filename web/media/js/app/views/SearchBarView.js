/**
 * Search bar
 *
 * @module     SearchBarView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['marionette', 'handlebars', 'App', 'text!templates/SearchBar.html'],
	function(Marionette, Handlebars, App, template)
	{
		return Marionette.ItemView.extend(
		{
			template : Handlebars.compile(template),
			collectionEvents : {
				'sync': 'render',
			},
			events:{
				'submit form': 'SearchPosts',
			},

			serializeData: function()
			{
				var data = {
					tags : this.collection.toJSON()
				};

				return data;
			},

			SearchPosts: function(e)
			{
				e.preventDefault();
				var keyword = this.$('#q').val(),
					tag = this.$('.js-select-tag-option option:selected').val();
				App.Collections.Posts.setFilterParams({
					q : keyword,
					tags : tag
				});
			}
		});
	});
