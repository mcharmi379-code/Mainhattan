# ICTECH CMS Bundle Element (Shopware 6.6) — User Guide

## 1. Overview
`ICTECHcmsBundleElement` is a Shopware 6.6 plugin that adds a bundle of custom CMS **blocks** and **elements** for Shopping Experiences.

It provides:
- Layout blocks (2–12 column variants)
- Content blocks (Accordion, Tabs, Feature Grid, Overview Cards, etc.)
- Navigation blocks (Breadcrumb, Subcategory Navigation)
- Commerce blocks (Product Matrix, Product Comparison)
- Media blocks (Image Gallery, Banner Slider, Category Image Slider, File Download)
- Hero / headline blocks (Hero Section, Hero Banner, Single Title + Buttons)

## 2. Requirements
- Shopware `^6.6`
- Administration build (the plugin ships Administration modules for CMS element/block configuration)

## 3. Installation / Activation (Admin)
1. Copy the plugin folder to:
   - `custom/plugins/ICTECHcmsBundleElement`
2. Install & activate the plugin in:
   - **Admin** -> **Extensions** -> **My extensions** -> `ICTECH CMS Bundle Element`

After activation, the new block categories and elements become available inside Shopping Experiences.

## 4. Where you use the plugin (Admin)
You use these blocks/elements in:
- **Admin** -> **Content** -> **Shopping Experiences**

You can apply Shopping Experiences to:
- Categories
- Landing pages
- (Depending on your Shopware setup) other page types supported by Shopping Experiences

### 4.1 Finding ICTECH blocks
In the Shopping Experiences block sidebar you will see ICTECH blocks grouped under categories like:
- `ict-cms-elements` (feature/content elements)
- `ict-cms-layouts` (layout/column blocks)

(Exact labels are controlled via Administration snippets.)

## 5. How the storefront renders these blocks
On the storefront, the plugin renders Twig templates from:
- `src/Resources/views/storefront/block/`
- `src/Resources/views/storefront/element/`

When you place a block in Shopping Experiences, Shopware creates CMS slots with the element configuration. The plugin’s PHP *CMS data resolvers* then enrich slot data (e.g., resolve media, categories, manufacturers, products) before Twig renders the final HTML.

## 6. Blocks and Elements (Element-wise documentation)

Below, every ICTECH element is documented with:
- **Purpose** (what it is for)
- **Admin flow** (how an editor configures it)
- **Storefront behavior** (what customers see)

---

## 6.1 Layout / Column Blocks (2 to 12)
These blocks are primarily used to build consistent multi-column sections.

### 6.1.1 `ict-two-column` (Block + Element)
- **Purpose**
  - A two-column block with a left and right slot, each slot is an `ict-two-column` element.
- **Admin flow**
  - Add block: **ICTECH layouts** -> `Two column`
  - You can assign content per slot (left/right). The element itself is built like an image card.
- **Storefront behavior**
  - Renders a card with optional image and text fields.
  - Storefront template: `cms-element-ict-two-column.html.twig`
- **Config fields used in storefront**
  - `media` (resolved via Shopware media resolver)
  - Optional text/link fields (if present in slot config):
    - `eyebrow`, `title`, `text`, `linkText`, `url`, `newTab`

### 6.1.2 `ict-three-column`, `ict-four-column`, `ict-five-column`, `ict-six-column`, `ict-seven-column`, `ict-eight-column`, `ict-nine-column`, `ict-ten-column`, `ict-eleven-column`, `ict-twelve-column`
- **Purpose**
  - Multi-column layout building blocks.
- **Admin flow**
  - Add a matching ICTECH layout block (3–12 columns) and configure each slot content.
  - Several of these elements are registered as `hidden: true` in Administration; you typically use them through their corresponding *block*, not by adding the element alone.
- **Storefront behavior**
  - Each column element renders a media card style variant.
  - Storefront templates exist for:
    - `ict-five-column`, `ict-six-column`, `ict-nine-column` (and are used by their blocks)

---

## 6.2 Content Elements

### 6.2.1 `ict-accordion`
- **Purpose**
  - A collapsible accordion component, useful for FAQs or grouped content.
- **Admin flow**
  - Add block: `ict-accordion`
  - Configure in element settings:
    - `title` (optional)
    - `description` (optional)
    - `displayMode` (`single` style behavior)
    - `entries` (repeatable list)
      - Each entry has:
        - `title`
        - `content` (HTML)
        - `expanded` (boolean)
- **Storefront behavior**
  - Renders heading/description and a list of accordion items.
  - Storefront template: `cms-element-ict-accordion.html.twig`

### 6.2.2 `ict-content-tabs`
- **Purpose**
  - Horizontal content tabs.
- **Admin flow**
  - Configure `tabs` (repeatable)
- **Storefront behavior**
  - Renders a tabs UI with content per tab.
  - Storefront template: `cms-element-ict-content-tabs.html.twig`

### 6.2.3 `ict-vertical-tab`
- **Purpose**
  - Vertical tabs (tab list at side, content area).
- **Admin flow**
  - Configure `tabs` list. Default example tab includes:
    - `title`, `header`, `content`, `buttonText`, `linkType`, `linkUrl`, `openInNewTab`
- **Storefront behavior**
  - Renders vertical tab navigation and content.
  - Storefront template: `cms-element-ict-vertical-tab.html.twig`

### 6.2.4 `ict-feature-grid`
- **Purpose**
  - A grid of feature cards.
- **Admin flow**
  - Configure:
    - `numberOfColumns`
    - `cardSpacing`
    - `cards` (repeatable card definitions)
- **Storefront behavior**
  - Renders a responsive grid of cards.
  - Storefront template: `cms-element-ict-feature-grid.html.twig`

### 6.2.5 `ict-overview-cards-6`
- **Purpose**
  - A highly configurable “overview cards” section, designed for up to 6 (or more) cards.
- **Admin flow**
  - Configure section background:
    - `backgroundType` (color/image/video/gradient)
    - `backgroundColor`, `backgroundImage`, `backgroundVideo`, `backgroundVideoUploadType`, `backgroundVideoUploadUrl`
    - `backgroundGradientColor1`, `backgroundGradientColor2`, `backgroundGradientDirection`
  - Configure layout:
    - `numberOfColumns`
    - `cardSpacing`
    - `verticalAlignment`
  - Configure `cards` (repeatable), based on `cardTemplates` schema (icons, headlines, detail rows, buttons, media positioning, per-card background types, etc.)
- **Storefront behavior**
  - Renders a grid of cards with optional:
    - Icon or image
    - Headline/subheadline/body text
    - Detail rows (key/value)
    - Buttons (internal/external)
    - Complex background configuration
  - Storefront template: `cms-element-ict-overview-cards-6.html.twig`

---

## 6.3 Media / Slider Elements

### 6.3.1 `ict-banner-slider`
- **Purpose**
  - A banner/image slider.
- **Admin flow**
  - Configure:
    - `sliderItems` (repeatable)
    - `autoTransition` (boolean)
    - `navigationDots` (e.g. `none`)
    - `navigationArrows` (e.g. `outside`)
    - `displayMode` (`contain` etc.)
    - `verticalAlign`
- **Storefront behavior**
  - Renders slider with configurable navigation.
  - Storefront template: `cms-element-ict-banner-slider.html.twig`

### 6.3.2 `ict-category-image-slider`
- **Purpose**
  - A slider that displays selected categories as “cards” (category image + link).
- **Admin flow**
  - Configure:
    - `cards` (repeatable)
    - `validationToken` (required field used internally)
- **Storefront behavior**
  - Resolves category entities and renders a slider/list of category cards.
  - Storefront template: `cms-element-ict-category-image-slider.html.twig`

### 6.3.3 `ict-image-gallery`
- **Purpose**
  - An image gallery with a configurable number of columns.
- **Admin flow**
  - Configure:
    - `galleryTitle`
    - `columns` (1–6)
    - `galleryItems` (repeatable list of media + metadata)
- **Storefront behavior**
  - Resolves referenced media and renders a gallery grid.
  - Storefront template: `cms-element-ict-image-gallery.html.twig`

### 6.3.4 `ict-file-download`
- **Purpose**
  - A file download component (e.g., PDF datasheet).
- **Admin flow**
  - Configure:
    - `title`, `subtitle`, `buttonText`
    - `showSpecifications` (boolean)
    - `mediaId` (downloadable file)
    - `previewImageId` (preview image)
- **Storefront behavior**
  - Shows a download section with optional preview image and button.
  - Storefront template: `cms-element-ict-file-download.html.twig`

---

## 6.4 Hero / CTA Elements

### 6.4.1 `ict-hero-section`
- **Purpose**
  - A hero section with:
    - Background (color/gradient)
    - Left content (label, headline, rich description, buttons)
    - Right-side “feature items” list
- **Admin flow**
  - Configure **Background** tab:
    - `backgroundType` (`color` or `gbColor` gradient)
    - `backgroundColor`
    - `backgroundGradientColor1`, `backgroundGradientColor2`
    - `minHeight`
  - Configure **Left section** tab:
    - Label: `labelText`, `labelBackgroundColor`, `labelTextColor`, `labelBorderColor`
    - Headline: `headlineText`, `headlineColor`
    - Description: `descriptionText` (rich text), `descriptionColor`
    - Primary button: text/link type/link/target/colors
      - `primaryButtonText`, `primaryButtonLinkType`, `primaryButtonLink`, `primaryButtonTarget`
      - `primaryButtonBackgroundColor`, `primaryButtonTextColor`
    - Secondary button (optional):
      - `showSecondaryButton`
      - `secondaryButtonText`, `secondaryButtonLinkType`, `secondaryButtonLink`, `secondaryButtonTarget`
      - `secondaryButtonBackgroundColor`, `secondaryButtonTextColor`
  - Configure **Right section** tab:
    - `featureItems` list (each has `title`, `description`)
- **Storefront behavior**
  - Renders a hero with CTA buttons and a feature list.
  - Automatically normalizes external links by adding `https://` when missing.
  - Storefront template: `cms-element-ict-hero-section.html.twig`

### 6.4.2 `ict-single-title-button-section`
- **Purpose**
  - A CTA section with a title/description and two buttons.
- **Admin flow**
  - Configure background:
    - `backgroundType` (color/gradient)
    - `backgroundColor`, `backgroundGradientColor1`, `backgroundGradientColor2`
    - `minHeight`
  - Configure text:
    - `title`, `titleColor`
    - `description` (HTML), `descriptionColor`
  - Configure left button:
    - `leftSectionButtonText`, `leftSectionButtonLinkType`, `leftSectionButtonLink`, `leftSectionButtonTarget`
    - `leftSectionButtonBackgroundColor`, `leftSectionButtonTextColor`
  - Configure right button:
    - `rightSectionButtonText`, `rightSectionButtonLinkType`, `rightSectionButtonLink`, `rightSectionButtonTarget`
    - `rightSectionButtonBackgroundColor`, `rightSectionButtonTextColor`
- **Storefront behavior**
  - Renders a two-CTA layout section.
  - Storefront template: `cms-element-ict-single-title-button-section.html.twig`

### 6.4.3 `ict-hero-banner`
- **Purpose**
  - A banner with a logo media + hero media + headline and background color.
- **Admin flow**
  - Configure:
    - `logoMediaId`
    - `heroMediaId`
    - `headline`
    - `backgroundColor`
- **Storefront behavior**
  - Renders banner visuals and headline.
  - Storefront template: `cms-element-ict-hero-banner.html.twig`

---

## 6.5 Navigation Elements

### 6.5.1 `ict-breadcrumb-navigation`
- **Purpose**
  - Breadcrumb UI element.
- **Admin flow**
  - Configure:
    - `alignment` (`left` etc.)
    - `textColor`
- **Storefront behavior**
  - Renders breadcrumb navigation with chosen alignment/color.
  - Storefront template: `cms-element-ict-breadcrumb-navigation.html.twig`

### 6.5.2 `ict-subcategory-navigation`
- **Purpose**
  - Renders navigation entries for subcategories.
- **Admin flow**
  - Configure:
    - `entryPoint` (`current` etc.)
    - `entryCategory` (category entity)
    - `manualCategories` (list)
    - `categoryLevels` (depth)
    - `showProductCount` (boolean)
- **Storefront behavior**
  - Resolves categories and optionally shows product count.
  - Storefront template: `cms-element-ict-subcategory-navigation.html.twig`

---

## 6.6 Commerce Elements

### 6.6.1 `ict-product-matrix`
- **Purpose**
  - Displays a matrix/table style view for a selected product.
- **Admin flow**
  - Configure:
    - `product` (entity selection)
    - `displayMode` (`standard`)
- **Storefront behavior**
  - Renders a product matrix.
  - Storefront template: `cms-element-ict-product-matrix.html.twig`

### 6.6.2 `ict-product-comparison`
- **Purpose**
  - Compares multiple products side-by-side.
- **Admin flow**
  - Choose assignment type:
    - `productAssignmentType`: `manual` or stream-based
  - Configure manual selection:
    - `productIds`
  - Configure product stream:
    - `productStreamId`, `productStreamSorting`, `productStreamLimit`
  - Configure comparison fields:
    - `defaultProductValues` (e.g. `name`, `price`)
    - `propertyGroups` (e.g. `color`, `size`)
  - Optional:
    - `highlightFirstProduct`
- **Storefront behavior**
  - Loads products (manual or stream) and renders comparison table.
  - Storefront template: `cms-element-ict-product-comparison.html.twig`

---

## 6.7 Manufacturer Elements

### 6.7.1 `ict-manufacturer-list`
- **Purpose**
  - Displays a list of manufacturers.
- **Admin flow**
  - No custom configuration panel (element is registered without `configComponent`).
- **Storefront behavior**
  - Renders manufacturers list.
  - Storefront template: `cms-element-ict-manufacturer-list.html.twig`

### 6.7.2 `ict-manufacturer-slider`
- **Purpose**
  - Displays selected manufacturers in a slider.
- **Admin flow**
  - Configure:
    - `manufacturerIds`
    - `showManufacturerName`
    - `maxWidth`, `maxHeight`
    - `desktopItems`, `tabletItems`, `mobileItems`
- **Storefront behavior**
  - Renders manufacturer logos and optional names.
  - Storefront template: `cms-element-ict-manufacturer-slider.html.twig`

---

## 6.8 Utility / Messaging Elements

### 6.8.1 `ict-alert-hint`
- **Purpose**
  - Show a styled alert/callout block (info/warning/etc.).
- **Admin flow**
  - Configure:
    - `alertType` (default `info`)
    - `layoutType` (default `custom-callout`)
    - `activateIcon` (boolean)
    - `title`
    - `content`
- **Storefront behavior**
  - Renders an alert box.
  - Storefront template: `cms-element-ict-alert-hint.html.twig`

### 6.8.2 `ict-story-card`
- **Purpose**
  - A card component with image and link.
- **Admin flow**
  - Configure:
    - `mediaId`
    - `title`, `subline`, `subtitle`
    - `linkType`, `linkUrl`, `openInNewTab`
- **Storefront behavior**
  - Renders a clickable card.
  - Storefront template: `cms-element-ict-story-card.html.twig`

---

## 6.9 Special Layout Element

### 6.9.1 `ict-two-column-three-image`
- **Purpose**
  - A 2-column layout featuring 3 images:
    - One large left image
    - Two stacked images on the right (top/bottom)
  - Each image can have its own button.
- **Admin flow**
  - Configure media and styling for:
    - Left image: `leftImage`, `leftImageHeight`, `leftImageObjectFit`, `leftImageDescription`
    - Left button: `leftButtonTitle`, `leftButtonLinkType`, `leftButtonLink`, `leftButtonTarget`, colors, icon and hover behavior
    - Right top image + button (similar fields)
    - Right bottom image + button (similar fields)
- **Storefront behavior**
  - Renders the 3-image layout with CTA buttons.
  - Storefront template: `cms-element-ict-two-column-three-image.html.twig`

---

## 7. Troubleshooting / Notes
- If an element shows placeholder content:
  - Ensure the block is saved and assigned to a page (category/landing).
  - Ensure required entities (media/category/products) are selected.
- Some “layout” elements are marked as `hidden` in Administration and are intended to be used via their corresponding block.

## 8. Technical reference (for developers)
- Plugin base class: `src/ICTECHcmsBundleElement.php`
- Services and resolvers: `src/Resources/config/services.xml`
- Storefront templates: `src/Resources/views/storefront/`
- Administration registrations: `src/Resources/app/administration/src/main.js`

