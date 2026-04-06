# 3.3.1
– Change: Dates in meta information/JSON-LD ISO 8601 formatted

# 3.3.0
- Adjustments for Shopware admin components from version SW >= 6.6.2

# 3.2.4
- Fix : The modal popup for images in additional items now also contains ALT/TITLE tags

# 3.2.3
– Fix: The modal popups (images) in additional content are working again.

# 3.2.2
– Change: HTML tags for headings reintroduced.

# 3.2.1
- Fix: correct handling of price queries in dynamic product groups

# 3.2.0
- Change: Plugin settings: Blog posts can optionally be excluded from sitemaps in all or individual sales channels

# 3.1.2
- Fix: CMS element blog listing: the category selection shows an assigned sales channel for a better overview.

# 3.1.1
- Fix: the selection "own template" in the blog listing element works again

# 3.1.0
- Change: Adaptation to our “Advanced search” plugin - Excluded search terms are taken into account

# 3.0.0
- Support for SW 6.6

# 2.2.0
- Change: A navigation category can optionally be set in the plugin settings and/or in blog categories; this is then used to display a breadcrumb on the blog detail page.
- Change: CMS element "Blog listing" - a specific blog post can be selected ("Number of posts" must be set to 1 and "No navigation" switched on)
- Change: CMS element "Blog listing" - several listing elements can co-exist on one CMS page (however, only one listing element may have a navigation/pagination)
- Change: "Link" field added to author profile (displayed in schema markup/JSON-LD and in EW author element) 
- Change: Support for admin search via Elasticsearch/OpenSearch
- Change: The Twig shortcode "{{ blog_media() }}" now also generates alt and title information of the image
- Change: EW element "Blog detail" - new image mode "responsive"
- Change: bin/console media:delete-unused correctly takes into account the images/media used in the blog

# 2.1.7
- Fix: The metadata (og:image) now also uses the thumbnail, if available. The largest available thumbnail is used.

# 2.1.6
- Fix: Headless data retrieval via Store API
- Fix: correct display of images/thumbnails in the detail view when a video is used

# 2.1.5
- Fix: For performance reasons, the maximum number of assigned products from a dynamic product group is limited to 25.
- Fix: Filtering of blogposts in the product detail page changed (performance issues with certain hosting environments).

# 2.1.4
- Fix: Canonical URLs corrected

# 2.1.3
- Fix: Slider navigation (left/right arrows) adjusted

# 2.1.2
- Fix: DAL adjustments for PHP 8.2

# 2.1.1
- Change: The title tag for blog posts in CMS elements is selectable (detail and listing elements)
- Change: Blog posts can be duplicated (context menu in list view)
- Change: Optionally a canonical URL can be set for each blog post (tab metadata)
- Change: Images / responsive thumbnail sizes optimized

# 2.0.1
- Internal: added missing DAL accesses
- Fix: Problems with CMS elements fixed after update
- 
# 2.0.0
- Support for SW 6.5
* +++ ATTENTION +++ **Update to SW 6.5**
* First deactivate all plugins (do not uninstall them!).
* Then update the store to SW 6.5
* Then update the plugins to the compatible version for SW 6.5
* Activate all plugins again
* Perform the update for each single plugin (click on the version number of each plugin)
* Shopware has made significant changes in version 6.5. The adaptation of our plugins here was very complex and took a lot of time.
* If something does not work as expected, please contact our plugin support at https://plugins.netzperfekt.de/support.

# 1.5.0
- Change: "big product image" setting for assigned products (in the experience world element blog detail)

# 1.4.1
- Fix: removed unnecessary URL parameters from blog detail page

# 1.4.0
- Integration with our plugin Advanced Search: link to "Show all search results".

# 1.3.4
- Fix: filter panel / show headline and close button for small resolution (smartphone, tablet)

# 1.3.3
- Fix: Individual product layouts: assigned blogposts are displayed correctly.

# 1.3.2
- Fix: Problem in admin with missing/deleted dynamic product group fixed.

# 1.3.1
- Fix: Problems related to other plugins fixed

# 1.3.0
(withdrawn due to an error)

# 1.3.0
- Change: "sticky" option for blog posts (always on top)
- Change: tags and categories are clickable (only on blog listing pages)
- Change: Support for video files on main image of a blog post 
- Change: dynamic product streams are optionally assignable to blog posts
- Fix: image gallery / the Shariff icons are hidden (if the plugin "NetzpShariff6" is installed)
- Fix: moved npm packages to Resources/app/storefront and Resources/app/administration

# 1.2.1
- Version: support for the new admin search in SW 6.4.8.0

# 1.2.0
- Change: Shopping experience block "blog Listing": pagination can optionally be switched off
- Fix: meta template / theme_config() is used
- Fix: Number of authors and categories in selection lists no longer limited to 25, search is working

# 1.1.24
- Change: new image mode "contain" for shopping worlds / blog detail page
- Fix: Tags can be assigned again in newly created blog posts
- Fix: non existing blog posts now throws a 404 error

# 1.1.23
- Fix: associated images/items are also deleted when a blog post is removed
- Fix: Store API route (BlogListing) modified

# 1.1.22
- Fix: shopware/build (javascript packages) is working again

# 1.1.21
- Change: support for access control lists (ACL), blog posts / authors / categories can be restricted (note: edit also the "detailed privileges" accordingly!)
- Fix: support for bin/console media:delete-unused
- Fix: rare problems with image uploading (additional items)

# 1.1.20
- Fix: better category caching / cms elements

# 1.1.19
- Fix: article detail / blogposts from other sales channels are correctly hidden
- Fix: category caching / cms elements

# 1.1.18
- Change: refactoring / better cache handling

# 1.1.17
- Fix: ESLint problems fixed when building assets / compiling

# 1.1.16
- Change: short code for blog contents {{ blog_media("tag", "...cssClasses...", "...cssStyles...") }}
- Version: support for SW 6.4.5.0

# 1.1.15
- Change: pagination: jump to begin of page
- Change: added some css classes to templates (post-meta-cms.html.twig; post-date / post-author / post-category / post-tags)
- Change: admin: product assignment - show product number
- Change: blog detail (cms): teaser is wrapped in <p> tag
- Change: removed slider from product details / blog tab due to shopware incompatibility - replaced with responsive grid
- Fix: sitemap generation is working again (SW 6.4.x)
- Fix: date formatting with correct locale
- Fix: title tag is set to post title if no meta title is given

# 1.1.14
- Fix: admin / assigned products / correct labels for variants
- Fix: admin / global search works again in SW 6.4

# 1.1.13
- Fix: SW 6.4 - correct uninstallation 

# 1.1.12
- Change: variant product assignment: if assigned to main product, blog posts will also show on variants
- Version: Support for SW 6.4

# 1.1.11
- Change: CMS listing filter (categories, tags, authors)
- Fix: correct sorting of shopware custom fields
- Fix: admin / blog category / cms page: list is now sorted
- Fix: cms element "blog products": removed layout mode "standard" (gave wrong layout)

# 1.1.10
- Fix: add blog sorting to rss feed
- Change: support for netzpShariff 1.0.2

# 1.1.9
- Change: optionally prevent blog posts from indexing 
- Change: shopping experience block "blog listing": added custom layout / twig templating
- Fix: rss feed respects the sales channel
- Fix: correct seourl on paginated blog lists
- Fix: better responsive layout for listing layout "list"

# 1.1.8
- Change: include in rss feed via category setting

# 1.1.7
- Change: multiple categories for blog posts
- Change: restrict blog category to sales channel
- Change: restrict blog category to customer group
- Change: restrict blog post to logged in users
- Fix: add meta (title/description, also for facebook and twitter) to custom layout blog posts

# 1.1.6
- Fix: not using scss relative paths anymore (./psh.phar storefront:hot-proxy has produced errors)
- Fix: variant products assignable to blog posts
- Fix: blog detail / proper url generation 
- Change: gallery is included in default blog layout

# 1.1.5
- Fix: display of blogposts in articles respects the time schedule

# 1.1.4
- Change: media gallery - support for documents / downloads + full image size zoom / thumbnail captions
- Change: open graph / twitter tags modified (blog title, description, image)

# 1.1.3
- Fix: correct path to node_modules (netzp-blog-gallery.plugin.js)

# 1.1.2
- Added: blogpost edit: tab "items" for recipes, guides, reviews etc.
- Added: blogpost edit: tab "image gallery"
- Added: support for standard shopware custom fields (attention: this is still a bit buggy in shopware overall)
- Change: blogpost edit: "images/media" moved to own tab
- Change: shopping experience blocks moved to own section 
- Change: shopping experience / blog detail element: product slider configurable (layout, display mode, minimum width)

# 1.1.1
- Fix: update / migration sometimes failed

# 1.1.0
- Change: select cms page / free blog layout (globally and/or per category)
- Change: smaller layout changes in backend
- Change: support for Shopware SEO Templates 
- Change: blog post can have an author (optional) / filtering by author in shopping experience
- Change: shopware pagination mechanism is being used
- Change: RSS feed (/blog.rss)
- Change: support for rich snippets
- Change: revised image thumbnail sizes
- Important: please clear ALL caches and regenerate the SEO index after update to 1.1.0!

# 1.0.15
- Fix: sitemap is built correctly when having more than 100 posts

# 1.0.14
- Change: additional twig blogs (element/blog-index* and page/blog/post*)

# 1.0.13
- Change: additional custom field (free to use in own templates: post.translated.custom)
- Change: support for tags

# 1.0.12
- Fix: sitemap generation works again

# 1.0.11
- Change: Limit search results in live search (only with our plugin Search Advanced)

# 1.0.10
- Change: search in administration
- Change: Filter by category in blog list

# 1.0.9
- Change: Support for our plugin "Search Advanced"
- Change: use thumbnails for preview images
- Change: optional preview image

# 1.0.8
- Fix: support for SW 6.2.2

# 1.0.7
- Change: product tab is only displayed if there are blog posts
- Change: blog list/layout cards: show only 2 columns on tablets
- Fix: product assignment: only the main products are displayed
- Fix: small layout fixes (blog list)

# 1.0.6
- Change: moved "blog categories" from settings/shop to settings/plugins
- Change: hide pagination when there's nothing to paginate ;-)
- Change: pagination: jump to blog content after page change
- Change: support for social sharing (with our plugin NetzpShariff)
- Fix: display problems in list view fixed

# 1.0.5
- Change: show related blog posts in product detail (optional)
- Change: paging in blog lists (set numberOfPosts > 0 in element config)
- Change: better image selection in blog editing

# 1.0.4
- Change: CMS blog element: better layout + background color option
- Change: CMS blog element: slider layout

# 1.0.3
- Fix: prices are shown on assigned products

# 1.0.2
- Change: Image layout: image size can be changed (contain, cover, auto)

# 1.0.1
- Change: Block and element for shopping experiences (URL /blog not working anymore)
- Change: Different layouts for index and detail view
- Change: Products can be assigned to blog posts
- Change: Blog post can be assigned to categories (defined in shop settings / blog categories)
- Change: Added twig blocks in templates

# 1.0.0
- initial version
