# MWArchimate
Display Archimate diagrams in MediaWiki

Version Alpha 0.1.3, this is still in development and experimental.

## Prerequisites



## Installation

After install, you will need to set up some dependencies within the Wiki.

In your LocalSettings.php, ensure you allow Archimate and XML uploads:
<pre>
$wgFileExtensions[] = 'archimate';
$wgFileExtensions[] = 'xml';
</pre>

'''Note''', MediaWiki blocks Archimate and XML, and at the time of typing I still have not been able to upload .xml files.

Due to the restrictions set in MediaWiki, you will need to allow passive uploading for Mime files:

<pre>
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
</pre>

You may also need to set the viewer path, although in my LocalSettings.php this was not required:

<pre>
$viewerPath = '/extensions/MWArchimate/modules/MWArchimateViewerandAnalyzer.html';
$viewerHtml = $wgScriptPath . $viewerPath;
</pre>

## Usage

This will create a special page Special:UploadMWArchimate.

Insert the Arhimate model using the tag:

<pre><MWArchimate file="YourModelFile.archimate"></MWArchimate></pre>

To set dimensions, use the tag:

<pre><MWArchimate file="YourModelFile.archimate" width="1200" height="800"></MWArchimate></pre>

© 2025 - DAFO Creative Ltd