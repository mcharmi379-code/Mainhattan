# 3.3.1
- Change: Datumsangaben in Metainformationen/JSON-LD ISO 8601 formattiert

# 3.3.0
- Anpassungen für Shopware Admin-Komponenten ab Version SW >= 6.6.2

# 3.2.4
- Fix: Das Modal-Popup für Bilder in Zusatzinhalten enthält nun auch ALT/TITLE-Tags

# 3.2.3
- Fix: Die Modal-Popups (Bilder) in Zusatzinhalten funktionieren wieder.

# 3.2.2
- Change: HTML-Tags für Überschriften wieder hinzugefügt.

# 3.2.1
- Fix: korrekte Behandlung von Preisabfragen in dynamischen Produktgruppen

# 3.2.0
- Change: Plugin-Settings: Blog-Posts können optional aus Sitemaps in allen oder einzelnen Verkaufskanälen ausgeschlossen werden

# 3.1.2
- Fix: CMS-Element Blog-Listing: die Kategorieauswahl zeigt für eine besser Übersicht einen eventuell zugeordneten Verkaufskanal an.

# 3.1.1
- Fix: die Auswahl "eigenes Template" im Blog-Listingelement funktioniert wieder

# 3.1.0
- Change: Anpassung an unser Plugin "Erweiterte Suche" - Ausgeschlossene Suchbegriffe werden berücksichtigt

# 3.0.0
- Support für SW 6.6

# 2.2.0
- Change: In den Plugin-Settings und/oder den Blog-Kategorien kann optional eine Navigations-Kategorie gesetzt werden, diese wird dann zur Anzeige einer Breadcrumb auf der Blog-Detailseite verwendet.
- Change: EW-Element "Blog-Listing" - es kann ein bestimmter Blogpost-ausgewählt werden ("Anzahl Posts" auf 1 setzen, "Keine Navigation" einschalten)
- Change: EW-Element "Blog-Listing" - es können mehrere Listing-Elemente auf einer EW-Seite co-existieren (dann darf allerdings nur ein Listing-Element eine Navigation/Paginierung haben)
- Change: Feld "Link" zum Autorenprofil hinzugefügt (Ausgabe im Schema-Markup/JSON-LD und im EW-Autoren-Element)
- Change: Unterstützung für die Admin-Suche via Elasticsearch/OpenSearch
- Change: Der Twig Shortcode "{{ blog_media() }}" erzeugt nun auch Alt- und Title-Angaben des Bildes
- Change: EW-Element "Blog-Detail" - neuer Bildmodus "responsiv"
- Change: bin/console media:delete-unused berücksichtigt die im Blog verwendeten Bilder/Medien korrekt

# 2.1.7
- Fix: Die Metadaten (og:image) verwenden jetzt auch das Vorschaubild, falls vorhanden. Es wird das größte vorhandene Thumbnail verwendet.

# 2.1.6
- Fix: Headless Datenabruf über Store-API korrigiert
- Fix: korrekte Anzeige von Bildern/Vorschaubildern in der Detailzeige, wenn ein Video verwendet wird

# 2.1.5
- Fix: Aus Geschwindigkeitsgründen ist die maximale Zahl von zugeordneten Produkten aus einer dynamischen Produktgruppe auf 25 begrenzt.
- Fix: Filterung der Blogposts in der Produktdetailseite geändert (Performanceprobleme bei bestimmten Hosting-Umgebungen)

# 2.1.4
- Fix: Canonical URLs korrigiert

# 2.1.3
- Fix: Slider-Navigationspfeile angepasst

# 2.1.2
- Fix: DAL-Anpassungen für PHP 8.2

# 2.1.1
- Change: Das Title-Tag für Blog-Posts in CMS-Elementen ist auswählbar (Detail und Listing-Elemente)
- Change: Blog-Posts können dupliziert werden (Kontextmenü in der Listenansicht)
- Change: Optional kann für jeden Blog-Post eine Canonical URL hinterlegt werden (Tab Metadaten)
- Change: Bilder / responsive Thumbnail-Größen optimiert

# 2.0.1
- Intern: fehlende DAL-Zugriffe hinzugefügt
- Fix: Probleme mit CMS-Elementen nach dem Update behoben

# 2.0.0
- Support für SW 6.5
* +++ ACHTUNG +++ **Aktualisierung auf SW 6.5**
* Deaktivieren Sie zunächst alle Plugins (nicht deinstallieren!)
* Aktualisieren Sie dann den Shop auf SW 6.5
* Aktualisieren Sie dann die Plugins auf die jeweils kompatible Version für SW 6.5
* Aktivieren Sie alle Plugins wieder
* Führen Sie das Update für jedes Plugin einzeln durch (klick auf die Versionsnummer des jeweiligen Plugins)
* Shopware hat in der Version 6.5 erhebliche Änderungen vorgenommen. Die Anpassung unserer Plugins war hier sehr aufwändig und hat viel Zeit beansprucht.
* Sollte etwas nicht wie bisher funktionieren, kontaktieren Sie bitte unseren Plugin-Support unter https://plugins.netzperfekt.de/support

# 1.5.0
- Change: Einstellung "großes Produktbild" für zugeordnete Produkte möglich (im Erlebniswelten-Element Blog Detail)

# 1.4.1
- Fix: unnötige URL-Parameter von der Blog-Detailseite entfernt

# 1.4.0
- Integration mit unserem Plugin Erweiterte Suche: Link auf "Zeige alle Suchergebnisse an"

# 1.3.4
- Fix: Filterpanel / Überschrift und Schließen-Button für kleine Auflösung (Smartphone, Tablet)

# 1.3.3
- Fix: Individuelle Produktlayouts: zugeordnete Blogposts werden korrekt angezeigt.

# 1.3.2
- Fix: Problem im Admin bei fehlender/gelöschter dynamischer Produktgruppe behoben

# 1.3.1
- Fix: Probleme im Zusammenhang mit anderen Plugins behoben
- Change: "sticky"-Option für Blog-Posts (immer oben)
- Change: Tags und Kategorien sind klickbar (nur auf Blog-Listing-Seiten)
- Change: Unterstützung für Video-Dateien im Hauptbild des Blog Posts
- Change: Optional kann einem Blog-Post auch eine dynamische Produktgruppe zugeordnet werden
- Fix: Bilder-Galerie / die Shariff-Icons werden ausgeblendet (falls das Plugin "NetzpShariff6" installiert ist)
- Fix: npm Pakete in die Ordner Resources/app/storefront bzw. Resources/app/administration verschoben

# 1.3.0
(wg. Fehler zurückgezogen)

# 1.2.1
- Version: Unterstützung der neuen Admin-Suche ab SW 6.4.8.0

# 1.2.0
- Change: EW-Block "Blog Listing": die Paginierung kann optional abgeschaltet werden
- Fix: Meta Template / theme_config() wird verwendet
- Fix: Anzahl der Autoren und Kategorien in Auswahllisten nicht mehr auf 25 beschränkt, Suche möglich

# 1.1.24
- Change: neuer Bildmodus "contain" für Einkaufswelten / Blog-Detailseite
- Fix: Tags können in neu erstellten Blog-Posts wieder zugewiesen werden
- Fix: nicht existierende Blog-Posts erzeugen jetzt einen 404-Fehler

# 1.1.23
- Fix: Zugeordnete Bilder/Items werden mit gelöscht, wenn ein Blog-Post entfernt wird
- Fix: Store API route (BlogListing) angepasst

# 1.1.22
- Fix: Shopware/Build (Javascript Pakete) funktioniert wieder

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
