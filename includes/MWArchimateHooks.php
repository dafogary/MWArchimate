<?php

use MediaWiki\MediaWikiServices;

class MWArchimateHooks {

    /**
     * Register only the <MWArchimate> tag for direct usage.
     */
    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'MWArchimate', [ self::class, 'renderMWArchimate' ] );
        return true;
    }

    /**
     * Handler for <MWArchimate file="..."/> tag.
     *
     * @param string $input (unused, as no wikitext body is needed)
     * @param array $args Tag arguments (should have 'file')
     * @param Parser $parser
     * @return string HTML to output
     */
    public static function renderMWArchimate( $input, $args, $parser ) {
        // Handle both direct tag usage and {{#tag:}} usage
        $fileName = '';
        $width = 900;
        $height = 600;
        
        // First check if we have parameters in $args (direct tag usage)
        if (isset($args['file'])) {
            $fileName = trim($args['file']);
            $width = isset($args['width']) ? intval($args['width']) : 900;
            $height = isset($args['height']) ? intval($args['height']) : 600;
        }
        // If not, parse the input content ({{#tag:}} usage)
        elseif (!empty($input)) {
            // Parse parameters from input content like "file=something|width=900|height=600"
            $params = array();
            $parts = explode('|', $input);
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, '=') !== false) {
                    list($key, $value) = explode('=', $part, 2);
                    $params[trim($key)] = trim($value);
                }
            }
            
            $fileName = isset($params['file']) ? $params['file'] : '';
            $width = isset($params['width']) ? intval($params['width']) : 900;
            $height = isset($params['height']) ? intval($params['height']) : 600;
        }

        if ( empty( $fileName ) ) {
            return '<div class="error">No MWArchimate model file specified.</div>';
        }
        
        return self::processArchimate( $fileName, $width, $height );
    }
    
    /**
     * Process the actual ArchiMate rendering
     */
    public static function processArchimate( $fileName, $width, $height ) {
        // Clean the filename (remove any remaining template syntax or whitespace)
        $fileName = preg_replace('/[{}]/', '', $fileName);
        $fileName = trim( $fileName );
        
        if ( empty( $fileName ) ) {
            return '<div class="error">Empty filename after cleaning template parameters.</div>';
        }

        // Try to resolve the file via MediaWiki File: namespace
        $title = Title::makeTitleSafe( NS_FILE, $fileName );
        if ( !$title || !$title->exists() ) {
            // Provide more detailed error information
            return '<div class="error">File not found: <strong>' . htmlspecialchars( $fileName ) . '</strong><br/>
                    Please check:<br/>
                    • File has been uploaded via Special:Upload or Special:ArchiMateUpload<br/>
                    • Filename is correct (including extension)<br/>
                    • If using templates/forms, ensure parameter has a value</div>';
        }
        
        // Use MediaWikiServices to get the RepoGroup service instead of wfFindFile
        $repoGroup = MediaWikiServices::getInstance()->getRepoGroup();
        $file = $repoGroup->findFile( $title );
        
        if ( !$file ) {
            return '<div class="error">Could not retrieve file: ' . htmlspecialchars( $fileName ) . '</div>';
        }
        $fileUrl = $file->getFullUrl();

        // Get script path using MediaWikiServices
        $scriptPath = MediaWikiServices::getInstance()->getMainConfig()->get('ScriptPath');
        
        // Path to the viewer HTML with proper URL encoding
        $viewerPath = '/extensions/MWArchimate/modules/MWArchimateViewerandAnalyzer.html';
        $viewerHtml = $scriptPath . $viewerPath;
        
        // If you still want debug information, use a comment in the returned HTML:
        $debugComment = "<!-- MWArchimate Viewer path: {$viewerHtml} -->";

        // Output an iframe embedding the viewer, passing the file URL via URL param
        $iframeSrc = $viewerHtml . '?model=' . urlencode( $fileUrl );

        return $debugComment . Html::element(
            'iframe',
            [
                'src' => $iframeSrc,
                'width' => $width,
                'height' => $height,
                'style' => "border:1px solid #ccc; box-shadow:0 2px 12px rgba(0,0,0,0.1);",
                'allowfullscreen' => 'true'
            ]
        );
    }
}