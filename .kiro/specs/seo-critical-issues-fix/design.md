# SEO Critical Issues Fix - Bugfix Design

## Overview

This design addresses 610 critical SEO issues affecting csnexplore.com, a PHP-based travel/car rental website. The site currently has only 10% indexability (25 out of 247 pages), with 201 pages returning 4xx errors, invalid HTML structure, poor content quality, and severe page speed issues (Desktop: 30/100, Mobile: 46/100).

The primary root cause is the HTML generation script (`php/api/generate_html.php`) which creates static HTML files for blogs and listing details. The fix will be targeted and minimal, focusing on correcting the generation logic while preserving all existing functionality.

**Fix Strategy:**
1. Correct HTTP status code handling in sitemap generation
2. Fix HTML structure issues (duplicate closing tags, content after `</html>`)
3. Improve content quality (titles, headings, alt tags, anchor text)
4. Implement page speed optimizations (lazy loading, image dimensions, WebP format)
5. Fix technical SEO issues (GTM, broken links, redirects)
6. Ensure all changes preserve existing functionality

## Glossary

- **Bug_Condition (C)**: The condition that triggers SEO issues - when static HTML files are generated with structural defects, poor content quality, or performance issues
- **Property (P)**: The desired behavior - generated HTML files should be valid, SEO-optimized, and performant
- **Preservation**: All existing functionality (booking system, authentication, admin panel, database operations, responsive design) must remain unchanged
- **generate_html.php**: The PHP script in `php/api/generate_html.php` that generates static HTML files for all blogs and listing items
- **generate_sitemap.php**: The PHP script in `php/api/generate_sitemap.php` that dynamically generates XML sitemaps
- **htmlHead()**: Function that generates the `<head>` section with meta tags, styles, and scripts
- **sharedHeader()**: Function that generates the site header navigation
- **sharedFooter()**: Function that generates the site footer
- **Static HTML Files**: Pre-generated HTML files stored in `/blogs/` and `/listing-detail/` directories
- **4xx Error**: HTTP client error status codes (404 Not Found, 403 Forbidden, etc.)
- **Orphan Pages**: Pages accessible only via sitemap, not through internal navigation links
- **Text-to-Code Ratio**: Percentage of readable text content compared to HTML/CSS/JS code
- **PageSpeed Score**: Google's performance metric (0-100) measuring page load speed and optimization

## Bug Details

### Bug Condition

The bug manifests when the `generate_html.php` script creates static HTML files for blogs and listings. The script produces multiple categories of defects:

**1. HTTP Status Code Issues:**
- 201 pages return 4xx errors instead of 200 OK
- 183 sitemap URLs return 4xx errors when accessed
- 185 orphan pages exist without internal navigation links

**2. HTML Structure Defects:**
- Content appears after `</html>` closing tag on 13 pages
- Multiple `</body>` and `</html>` closing tags on multiple pages
- 32 pages have mismatched canonical URLs

**3. Content Quality Issues:**
- 25 pages have titles shorter than SEO-recommended length
- 12 pages have H1 tags starting with lowercase letters
- 24 pages have H2 tags starting with lowercase letters
- 16 pages have HTML tags nested inside H1 tags
- 26 pages have HTML tags nested inside H2 tags
- 43 pages have one-word alt tags
- 43 pages have one-word anchor text
- 34 pages have links with no anchor text
- 43 pages have text-to-code ratio less than 10%

**4. Duplicate Content Issues:**
- 22 pages have identical heading text
- 43 pages have identical alt tag text

**5. Page Speed Issues:**
- Desktop PageSpeed score: 30/100
- Mobile PageSpeed score: 46/100
- 43 pages lack image width/height dimensions
- 43 pages don't defer offscreen images
- 43 pages serve legacy image formats (JPEG/PNG) instead of WebP
- 43 pages contain excessive HTML comments (>1000 characters)

**6. Technical SEO Issues:**
- 43 pages missing GTM implementation
- 43 pages contain links to 3xx redirect URLs
- 28 pages contain links to 4xx error URLs
- 1 URL contains whitespace characters

**7. Redirect Issues:**
- Broken redirect chains exist
- Self-redirecting URLs exist

**Formal Specification:**
```
FUNCTION isBugCondition(generatedFile)
  INPUT: generatedFile of type HTMLFile
  OUTPUT: boolean
  
  RETURN (
    hasHTTPStatusError(generatedFile) OR
    hasInvalidHTMLStructure(generatedFile) OR
    hasPoorContentQuality(generatedFile) OR
    hasDuplicateContent(generatedFile) OR
    hasPageSpeedIssues(generatedFile) OR
    hasTechnicalSEOIssues(generatedFile) OR
    hasRedirectIssues(generatedFile)
  )
  
  WHERE:
    hasHTTPStatusError(file) = (file.statusCode IN [400, 403, 404, 410] OR file.isOrphan)
    hasInvalidHTMLStructure(file) = (file.hasContentAfterHTMLTag OR file.hasDuplicateClosingTags OR file.hasCanonicalMismatch)
    hasPoorContentQuality(file) = (file.titleLength < 50 OR file.hasLowercaseHeadings OR file.hasNestedHTMLInHeadings OR file.hasOneWordAltTags OR file.hasOneWordAnchorText OR file.hasEmptyAnchorText OR file.textToCodeRatio < 10)
    hasDuplicateContent(file) = (file.hasIdenticalHeadings OR file.hasIdenticalAltTags)
    hasPageSpeedIssues(file) = (file.pageSpeedScore < 70 OR file.lacksImageDimensions OR file.lacksLazyLoading OR file.usesLegacyImageFormats OR file.hasExcessiveComments)
    hasTechnicalSEOIssues(file) = (file.missingGTM OR file.hasBrokenInternalLinks OR file.hasURLWithWhitespace)
    hasRedirectIssues(file) = (file.hasBrokenRedirectChain OR file.hasSelfRedirect)
END FUNCTION
```

### Examples

**Example 1: Invalid HTML Structure**
- **File**: `listing-detail/cars-9-tata-tiago.html`
- **Current Behavior**: Content appears after `</html>` closing tag due to footer function appending content incorrectly
- **Expected Behavior**: All content should be within `<html>...</html>` tags with exactly one `</body>` and one `</html>` closing tag

**Example 2: Poor Content Quality**
- **File**: `blogs/blogs-1-complete-travel-guide.html`
- **Current Behavior**: Title is 35 characters (too short), H1 starts with lowercase "complete", alt tags are "image1", "image2" (one-word)
- **Expected Behavior**: Title should be 50-60 characters, H1 should start with uppercase "Complete", alt tags should be descriptive (e.g., "Complete travel guide to Chhatrapati Sambhajinagar showing historical monuments")

**Example 3: Page Speed Issues**
- **File**: `listing-detail/cars-10-kia-carens.html`
- **Current Behavior**: Images lack width/height attributes, all images load immediately (no lazy loading), images are JPEG format, PageSpeed score 30/100
- **Expected Behavior**: Images should have explicit dimensions, offscreen images should use `loading="lazy"`, images should be WebP with JPEG fallback, PageSpeed score should be 70+/100

**Example 4: HTTP Status Code Issues**
- **File**: `sitemap-blogs.xml` includes URL `blogs/blogs-999-deleted-post.html`
- **Current Behavior**: URL returns 404 error but is still listed in sitemap
- **Expected Behavior**: Sitemap should only include URLs that return 200 OK status

**Example 5: Technical SEO Issues**
- **File**: `listing-detail/bikes-5-royal-enfield.html`
- **Current Behavior**: Missing GTM code, contains link to `/old-page` which redirects to `/new-page` (3xx), contains link to `/deleted-page` which returns 404 (4xx)
- **Expected Behavior**: GTM code should be present, internal links should point directly to final destinations (no redirects), all internal links should point to valid pages (no 4xx errors)

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- All existing booking functionality must continue to work (form submission, authentication, guest booking)
- All existing authentication and user management must continue to work
- All existing admin panel functionality must continue to work
- All existing database operations must continue to work correctly
- All existing responsive design and mobile functionality must continue to work
- All existing JavaScript functionality (slideshow, lightbox, form validation) must continue to work
- All existing CSS styling and animations must continue to work
- All existing page layouts and visual design must remain unchanged
- All existing URL structures and routing must remain unchanged
- All existing API endpoints must continue to function correctly

**Scope:**
All inputs that do NOT involve the HTML generation process should be completely unaffected by this fix. This includes:
- User interactions with existing pages (clicks, form submissions, navigation)
- Database queries and data retrieval
- Authentication and authorization flows
- Admin panel operations
- API requests and responses
- Real-time features (if any)

**Note:** The actual expected correct behavior for generated HTML files is defined in the Correctness Properties section (Property 1). This section focuses on what must NOT change.

## Hypothesized Root Cause

Based on the bug description and code analysis, the most likely issues are:

### 1. **HTML Structure Issues - Footer Concatenation**
The `sharedFooter()` function returns HTML that is concatenated after the main content, but the main content already includes `</body></html>` tags. This causes content to appear after the closing `</html>` tag.

**Evidence**: In `generate_html.php` line ~1400+, the pattern is:
```php
$html .= '</main>';
$html .= sharedFooter('../');
```
But `</main>` is followed by `</body></html>` in the main content, then footer HTML is appended.

**Fix**: Ensure footer is inserted BEFORE the closing `</body></html>` tags, not after.

### 2. **Content Quality Issues - Insufficient Validation**
The script generates titles, headings, and alt tags directly from database content without validation or enhancement:
- Titles are taken directly from `$blog['title']` or `$item['name']` without length checks
- Headings use raw database content without capitalization checks
- Alt tags use generic patterns like "Blog Hero" or simple concatenation
- Anchor text uses minimal descriptive text

**Evidence**: In `generate_html.php`:
```php
$html = htmlHead(htmlspecialchars($blog['title']) . ' | CSNExplore', ...);
// No length validation or enhancement
```

**Fix**: Add validation and enhancement functions for titles, headings, alt tags, and anchor text.

### 3. **Page Speed Issues - Missing Optimizations**
The script generates image tags without performance attributes:
- No `width` and `height` attributes on images
- No `loading="lazy"` attribute for offscreen images
- No WebP format generation or `<picture>` elements for next-gen formats
- Excessive inline styles and comments in generated HTML

**Evidence**: In `generate_html.php`:
```php
<img src="'.htmlspecialchars($mainImg).'" alt="Blog Hero" class="w-full h-full object-cover" loading="lazy"/>
// Missing width/height, lazy loading only on some images
```

**Fix**: Add image dimensions, implement lazy loading consistently, generate WebP versions, minimize inline content.

### 4. **Sitemap Issues - No Status Code Validation**
The `generate_sitemap.php` script includes all database records without checking if the corresponding HTML files exist or are accessible:
```php
$blogs = $db->fetchAll("SELECT * FROM blogs WHERE status='published' ORDER BY id DESC LIMIT 600");
foreach ($blogs as $blog) {
    $urls[] = ['loc' => 'blogs/' . $slug, ...];
}
```

**Fix**: Validate that HTML files exist and are accessible before including in sitemap.

### 5. **Technical SEO Issues - Missing GTM and Link Validation**
- GTM code is not included in the `htmlHead()` function
- Internal links are generated without validation of target URLs
- No check for redirect chains or broken links

**Fix**: Add GTM code to `htmlHead()`, implement link validation before generation.

### 6. **Orphan Pages - Missing Internal Navigation**
185 pages are accessible only via sitemap because they lack internal navigation links. This suggests:
- Related items/posts sections may not be generating correctly
- Category/tag navigation may be incomplete
- Breadcrumb links may be missing or incorrect

**Fix**: Ensure all pages have proper internal navigation (related items, breadcrumbs, category links).

## Correctness Properties

Property 1: Bug Condition - Generated HTML Files Are Valid and SEO-Optimized

_For any_ HTML file generated by `generate_html.php` where the bug condition holds (isBugCondition returns true), the fixed generation script SHALL produce valid HTML with proper structure (no content after `</html>`, exactly one closing tag for each element), SEO-optimized content (titles 50-60 characters, proper heading capitalization, descriptive alt tags and anchor text, text-to-code ratio ≥15%), performance optimizations (image dimensions, lazy loading, WebP format), and technical SEO compliance (GTM code, valid internal links, no redirects or 4xx errors).

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 2.10, 2.11, 2.12, 2.13, 2.14, 2.15, 2.16, 2.17, 2.18, 2.19, 2.20, 2.21, 2.22, 2.23, 2.24, 2.25, 2.26, 2.27, 2.28, 2.29**

Property 2: Preservation - Non-Generation Functionality Unchanged

_For any_ functionality that is NOT part of the HTML generation process (booking system, authentication, admin panel, database operations, API endpoints, user interactions), the fixed code SHALL produce exactly the same behavior as the original code, preserving all existing features, layouts, styling, and functionality.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12, 3.13, 3.14, 3.15**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct:

**File**: `php/api/generate_html.php`

**Function**: Multiple functions need modification

**Specific Changes**:

#### 1. **Fix HTML Structure Issues**
   - **Location**: `htmlHead()` function and main generation loops
   - **Change**: Ensure footer is inserted BEFORE closing `</body></html>` tags
   - **Implementation**:
     ```php
     // Instead of:
     $html .= '</main>';
     $html .= sharedFooter('../');
     
     // Do:
     $html .= '</main>';
     $html .= sharedFooter('../');
     $html .= '</body></html>';
     // And remove duplicate closing tags from other locations
     ```

#### 2. **Add Content Quality Validation Functions**
   - **Location**: Top of file, before main generation logic
   - **Change**: Add helper functions for content validation and enhancement
   - **Implementation**:
     ```php
     function optimizeTitle($title, $suffix = ' | CSNExplore') {
         $title = trim($title);
         $withSuffix = $title . $suffix;
         if (strlen($withSuffix) < 50) {
             // Pad with descriptive text
             $title = $title . ' - Best Deals in Chhatrapati Sambhajinagar';
         } elseif (strlen($withSuffix) > 60) {
             // Truncate
             $title = substr($title, 0, 60 - strlen($suffix) - 3) . '...';
         }
         return $title . $suffix;
     }
     
     function capitalizeHeading($text) {
         return ucfirst(trim($text));
     }
     
     function generateDescriptiveAlt($context, $itemName, $index = 0) {
         $descriptors = ['showing', 'featuring', 'displaying', 'highlighting'];
         $descriptor = $descriptors[$index % count($descriptors)];
         return ucfirst($context) . ' ' . $descriptor . ' ' . $itemName . ' in Chhatrapati Sambhajinagar';
     }
     
     function generateDescriptiveAnchor($itemName, $type) {
         return 'View ' . $itemName . ' ' . $type . ' details';
     }
     ```

#### 3. **Implement Page Speed Optimizations**
   - **Location**: Image generation code throughout the file
   - **Change**: Add image dimensions, lazy loading, and WebP support
   - **Implementation**:
     ```php
     function generateOptimizedImage($src, $alt, $width = 800, $height = 600, $lazy = true) {
         $webpSrc = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src);
         $lazyAttr = $lazy ? ' loading="lazy"' : '';
         
         return '<picture>' .
                '<source srcset="' . htmlspecialchars($webpSrc) . '" type="image/webp">' .
                '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '" ' .
                'width="' . $width . '" height="' . $height . '"' . $lazyAttr . ' ' .
                'onerror="this.src=\'../images/travelhub.png\'">' .
                '</picture>';
     }
     ```

#### 4. **Add GTM Code to htmlHead()**
   - **Location**: `htmlHead()` function, after opening `<head>` tag
   - **Change**: Insert Google Tag Manager code
   - **Implementation**:
     ```php
     $head = '<!DOCTYPE html>
     <html class="light" lang="en" style="scroll-behavior:smooth">
     <head>
     <!-- Google Tag Manager -->
     <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
     new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
     j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
     \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
     })(window,document,\'script\',\'dataLayer\',\'GTM-XXXXXXX\');</script>
     <!-- End Google Tag Manager -->
     <meta charset="utf-8"/>';
     ```

#### 5. **Fix Sitemap Generation with Status Validation**
   - **Location**: `php/api/generate_sitemap.php`
   - **Change**: Validate HTML files exist before including in sitemap
   - **Implementation**:
     ```php
     foreach ($blogs as $blog) {
         $slug = $blog['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $blog['title']), '-'));
         $file = dirname(__DIR__, 2) . '/blogs/' . $slug . '.html';
         
         // Only include if file exists and is readable
         if (file_exists($file) && is_readable($file)) {
             $lastmod = substr($blog['updated_at'] ?? $today, 0, 10);
             $urls[] = ['loc' => 'blogs/' . $slug, 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $lastmod];
         }
     }
     ```

#### 6. **Add Internal Navigation Links**
   - **Location**: Blog and listing detail generation sections
   - **Change**: Ensure related items, breadcrumbs, and category links are always present
   - **Implementation**: Already partially implemented, but ensure:
     - Related items section always shows (even if only 1-2 items)
     - Breadcrumbs are complete and accurate
     - Category/tag links are present and functional

#### 7. **Minimize HTML Comments and Inline Styles**
   - **Location**: Throughout generated HTML
   - **Change**: Remove excessive comments, move inline styles to external CSS where possible
   - **Implementation**: Review and remove debug comments, consolidate repeated inline styles

#### 8. **Add Link Validation**
   - **Location**: Before generating internal links
   - **Change**: Validate that target URLs exist and don't redirect
   - **Implementation**:
     ```php
     function validateInternalLink($url, $baseDir) {
         $filePath = $baseDir . '/' . ltrim($url, '/');
         
         // Check if file exists
         if (!file_exists($filePath)) {
             return false;
         }
         
         // Check if it's a redirect (would need .htaccess parsing or HTTP check)
         // For now, just ensure file exists
         return true;
     }
     ```

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bugs on unfixed code, then verify the fix works correctly and preserves existing behavior.

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bugs BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: 
1. Run the current `generate_html.php` script to generate HTML files
2. Analyze generated files for structural issues, content quality issues, and performance issues
3. Run HTML validators and SEO audit tools on generated files
4. Check sitemap for 4xx errors
5. Measure PageSpeed scores for sample pages

**Test Cases**:
1. **HTML Structure Test**: Parse generated HTML files and check for content after `</html>`, duplicate closing tags (will fail on unfixed code)
2. **Content Quality Test**: Check title lengths, heading capitalization, alt tag descriptiveness, anchor text quality (will fail on unfixed code)
3. **Page Speed Test**: Measure PageSpeed scores, check for image dimensions, lazy loading, WebP format (will fail on unfixed code)
4. **Sitemap Validation Test**: Check all sitemap URLs return 200 OK (will fail on unfixed code - 183 URLs return 4xx)
5. **GTM Test**: Check for GTM code in generated HTML (will fail on unfixed code - 43 pages missing GTM)

**Expected Counterexamples**:
- HTML files with content after `</html>` tag
- Titles shorter than 50 characters or longer than 60 characters
- Headings starting with lowercase letters
- One-word alt tags and anchor text
- Images without width/height attributes
- Images without lazy loading
- No WebP format images
- PageSpeed scores below 70/100
- Sitemap URLs returning 404 errors
- Missing GTM code

**Possible Root Causes Confirmed**:
- Footer concatenation after closing tags
- No content validation or enhancement
- Missing image optimization attributes
- No sitemap URL validation
- Missing GTM code in htmlHead()

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds, the fixed function produces the expected behavior.

**Pseudocode:**
```
FOR ALL generatedFile WHERE isBugCondition(generatedFile) DO
  result := generate_html_fixed(generatedFile.source)
  ASSERT expectedBehavior(result)
END FOR

WHERE expectedBehavior(result) =
  result.hasValidHTMLStructure AND
  result.hasOptimizedContent AND
  result.hasPageSpeedOptimizations AND
  result.hasTechnicalSEOCompliance AND
  result.statusCode == 200
```

**Test Plan**:
1. Run the fixed `generate_html.php` script
2. Validate all generated HTML files for structural correctness
3. Check content quality metrics (title length, heading capitalization, alt tags, anchor text)
4. Measure PageSpeed scores for sample pages
5. Validate sitemap only includes 200 OK URLs
6. Check for GTM code presence
7. Validate internal links point to valid destinations

**Test Cases**:
1. **HTML Structure Validation**: Parse all generated HTML files, ensure no content after `</html>`, exactly one closing tag for each element
2. **Content Quality Validation**: Check all titles are 50-60 characters, all headings start with uppercase, all alt tags are descriptive (3+ words), all anchor text is descriptive (2+ words)
3. **Page Speed Validation**: Measure PageSpeed scores (should be 70+/100), check all images have dimensions, lazy loading, WebP format
4. **Sitemap Validation**: Check all sitemap URLs return 200 OK
5. **GTM Validation**: Check all pages have GTM code
6. **Link Validation**: Check all internal links point to valid pages (no 4xx errors, no redirects)

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold, the fixed function produces the same result as the original function.

**Pseudocode:**
```
FOR ALL functionality WHERE NOT isHTMLGeneration(functionality) DO
  ASSERT originalBehavior(functionality) = fixedBehavior(functionality)
END FOR

WHERE isHTMLGeneration(functionality) =
  functionality IN [generate_html.php, generate_sitemap.php]
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across the input domain
- It catches edge cases that manual unit tests might miss
- It provides strong guarantees that behavior is unchanged for all non-generation functionality

**Test Plan**: 
1. Test booking functionality on generated pages (form submission, authentication, guest booking)
2. Test user authentication flows (login, logout, session management)
3. Test admin panel operations (CRUD operations, regeneration triggers)
4. Test database operations (queries, inserts, updates, deletes)
5. Test API endpoints (bookings, listings, blogs)
6. Test responsive design and mobile functionality
7. Test JavaScript functionality (slideshow, lightbox, form validation)
8. Test CSS styling and animations

**Test Cases**:
1. **Booking Preservation**: Submit booking forms on generated pages, verify bookings are created correctly in database
2. **Authentication Preservation**: Login/logout on generated pages, verify session management works correctly
3. **Admin Panel Preservation**: Perform CRUD operations in admin panel, verify database updates correctly
4. **Database Preservation**: Run database queries, verify results match expected data
5. **API Preservation**: Call API endpoints, verify responses match expected format and data
6. **Responsive Design Preservation**: View generated pages on mobile devices, verify layout and functionality work correctly
7. **JavaScript Preservation**: Interact with JavaScript features (slideshow, lightbox), verify they work correctly
8. **CSS Preservation**: Check visual styling on generated pages, verify it matches original design

### Unit Tests

- Test `optimizeTitle()` function with various input lengths
- Test `capitalizeHeading()` function with lowercase and mixed-case inputs
- Test `generateDescriptiveAlt()` function with various contexts and item names
- Test `generateDescriptiveAnchor()` function with various item names and types
- Test `generateOptimizedImage()` function with various image sources and dimensions
- Test sitemap URL validation logic with existing and non-existing files
- Test HTML structure validation (no content after `</html>`, no duplicate closing tags)
- Test link validation logic with valid and invalid URLs

### Property-Based Tests

- Generate random blog titles and verify `optimizeTitle()` always produces 50-60 character titles
- Generate random heading text and verify `capitalizeHeading()` always produces properly capitalized headings
- Generate random image contexts and verify `generateDescriptiveAlt()` always produces descriptive (3+ word) alt tags
- Generate random item names and verify `generateDescriptiveAnchor()` always produces descriptive (2+ word) anchor text
- Generate random image sources and verify `generateOptimizedImage()` always includes width, height, and lazy loading attributes
- Generate random database records and verify sitemap only includes URLs for existing files
- Generate random HTML content and verify no content appears after `</html>` tag
- Generate random internal links and verify all point to valid destinations

### Integration Tests

- Generate full set of blog HTML files and verify all pass HTML validation
- Generate full set of listing detail HTML files and verify all pass HTML validation
- Generate sitemap and verify all URLs return 200 OK
- Measure PageSpeed scores for sample pages and verify all score 70+/100
- Submit booking on generated page and verify booking is created in database
- Login on generated page and verify session is created correctly
- Navigate through generated pages and verify all internal links work correctly
- View generated pages on mobile device and verify responsive design works correctly
