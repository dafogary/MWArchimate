# MWArchimate
Display Archimate diagrams in MediaWiki

Version Alpha 0.1.4, this is still in development and experimental.

## Prerequisites
To be completed.


## Installation

After install, you will need to set up some dependencies within the Wiki.

In your LocalSettings.php, ensure you allow Archimate and XML uploads:
```html
$wgFileExtensions[] = 'archimate';
$wgFileExtensions[] = 'xml';
```

'''Note''', MediaWiki blocks Archimate and XML, and at the time of typing I still have not been able to upload .xml files.

Due to the restrictions set in MediaWiki, you will need to allow passive uploading for Mime files:

```html
//Archimate
// More permissive file upload settings
$wgStrictFileExtensions = false;
$wgCheckFileExtensions = false;
$wgAllowJavaUploads = true;
$wgVerifyMimeType = false;
$wgMimeTypeExclusions = [];

// Explicitly allow XML and Archimate files
$wgFileExtensions[] = 'archimate';
$wgFileExtensions[] = 'xml';
```

You may also need to set the viewer path, although in my LocalSettings.php this was not required:

```html
$viewerPath = '/extensions/MWArchimate/modules/MWArchimateViewerandAnalyzer.html';
$viewerHtml = $wgScriptPath . $viewerPath;
```

## Usage

This extension provides multiple ways to embed ArchiMate diagrams in your MediaWiki pages.

### Direct Tag Usage

Insert the ArchiMate model using the tag:

```html
<MWArchimate file="YourModelFile.archimate"></MWArchimate>
```

To set dimensions, use the tag:

```html
<MWArchimate file="YourModelFile.archimate" width="1200" height="800"></MWArchimate>
```

### Template Usage with {{#tag:}} Syntax

For use within MediaWiki templates (especially with PageForms), use the `{{#tag:}}` syntax:

```wikitext
{{#tag:MWArchimate|file={{{Archimate file}}}|width={{{width|900}}}|height={{{height|600}}}}}
```

### Example Template

Create a template called `Template:ArchimateTemplate`:

```wikitext
'''Project:''' {{{Project name|No project specified}}}

'''ArchiMate Model:'''
{{#tag:MWArchimate|file={{{Archimate file}}}|width={{{width|900}}}|height={{{height|600}}}}}

[[Category:Archimate Projects]]
```

Then use it in pages:

```wikitext
{{ArchimateTemplate
|Project name=My Project
|Archimate file=MyModel.archimate
|width=1200
|height=800
}}
```

### File Upload

Files can be uploaded via:
- `Special:Upload` (standard MediaWiki file upload)
- `Special:ArchiMateUpload` (dedicated ArchiMate upload page)

## Troubleshooting

### Templates and Forms

If you're using MediaWiki templates or PageForms, **always use the `{{#tag:}}` syntax** instead of direct `<MWArchimate>` tags within templates.

**Correct template syntax:**
```wikitext
{{#tag:MWArchimate|file={{{Archimate file}}}|width={{{width|900}}}|height={{{height|600}}}}}
```

**Incorrect template syntax (will not work):**
```html
<MWArchimate file="{{{Archimate file}}}"></MWArchimate>
```

**Why:** MediaWiki processes parser tags before resolving template parameters, so `{{{parameter}}}` syntax inside `<tags>` doesn't get resolved properly. The `{{#tag:}}` syntax ensures template parameters are resolved first.

**Common solutions:**

1. **Use {{#tag:}} syntax in templates**: Always use the format shown above for template integration.

2. **Ensure the parameter has a value**: When calling your template, make sure you provide a value:
   ```wikitext
   {{Your Template|Archimate file=MyModel.archimate}}
   ```

3. **Check parameter names**: Ensure the parameter name matches exactly (case-sensitive).

4. **For PageForms**: Make sure the form field name matches the template parameter name exactly.

### File Upload Issues

- Files must be uploaded via `Special:Upload` or `Special:ArchiMateUpload`
- File extension must be `.archimate`
- Check that `$wgFileExtensions` includes `'archimate'` in LocalSettings.php
- Ensure the file exists in the File: namespace (check `Special:ListFiles`)

### PageForms Integration

Example form field for uploading ArchiMate files:

```wikitext
{{{field|Archimate file|uploadable|values from category=Archimate Projects}}}
```

This creates an uploadable field that users can use to upload `.archimate` files directly from the form.

© 2025 - DAFO Creative Ltd