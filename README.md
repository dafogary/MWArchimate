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

## Guidance

This will create a special page Special:UploadMWArchimate.

Insert the Arhimate model using the tag:

<pre><mwarchimate file="yourfile.xml" /></pre>


© 2025 - DAFO Creative Ltd
