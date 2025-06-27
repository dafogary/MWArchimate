<?php

use MediaWiki\MediaWikiServices;

class MWArchimateHooks {

    /**
     * Register the <MWArchimate> tag.
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
        $fileName = isset( $args['file'] ) ? htmlspecialchars( $args['file'] ) : '';
        $width = isset( $args['width'] ) ? intval( $args['width'] ) : 900;
        $height = isset( $args['height'] ) ? intval( $args['height'] ) : 600;

        if ( !$fileName ) {
            return '<div class="error">No MWArchimate model file specified.</div>';
        }

        // Try to resolve the file via MediaWiki File: namespace
        $title = Title::makeTitleSafe( NS_FILE, $fileName );
        if ( !$title || !$title->exists() ) {
            return '<div class="error">File not found: ' . htmlspecialchars( $fileName ) . '</div>';
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
        
        // Remove the problematic debug line
        // $parser->getOutput()->addHTML("<!-- Full path: {$viewerHtml} -->");
        
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