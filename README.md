# MWArchimate
Display Archimate diagrams in MediaWiki

Version Alpha 0.1, this is still in development and experimental.

## Prerequisites

This Extension requires Page Forms of FlexForms (although not tested with FlexForms) and Semantic MediaWiki.

## Postrequisites

After install, you will need to set up some dependencies within the Wiki.

In your LocalSettings.php, ensure you allow XML uploads:
<pre>
<?php
// filepath: LocalSettings.php
$wgFileExtensions[] = 'xml';
</pre>

Create a Template:ArchimateDiagram
<pre>
<!-- Template:ArchimateDiagram -->
<div class="archimate-diagram">
  <pre class="mw-code mw-xml">
    {{{Has XML content}}}
  </pre>
</div>
</pre>

Create a property for XML content:

Required property: Property:Has XML content, this will need to have type Text.

You will also need to create a form, for example, Archimate diagram:

<pre>
<noinclude>
This is the "Archimate Diagrams" form.
To create a page with this form, enter the page name below;
if a page with that name already exists, you will be sent to a form to edit that page.

{{#forminput:form=Archimate Diagrams|query string=namespace=Archimate}}

</noinclude><includeonly>
<div id="wikiPreview" style="display: none; padding-bottom: 25px; margin-bottom: 25px; border-bottom: 1px solid #AAAAAA;"></div>
{{{for template|ArchimateDiagram}}}
{| class="formtable"
! Has XML content: 
|-
| {{{field|Has XML content|uploadable|values from namespace=File}}}
|}
{{{end template}}}

</includeonly>
</pre>


© 2025 - DAFO Creative Ltd
