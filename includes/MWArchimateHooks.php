<?php

class MWArchimateHooks {
    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'mwarchimate', [ self::class, 'renderViewer' ] );
        return true;
    }

    public static function renderViewer( $input, array $args, Parser $parser, PPFrame $frame ) {
        $file = $args['file'] ?? '';
        $path = __DIR__ . "/../uploads/$file";

        if ( !$file || !file_exists( $path ) ) {
            return '<div class="error">MWArchimate XML file not found</div>';
        }

        $xml = file_get_contents( $path );
        error_log("MWArchimate: XML = " . $xml);
        $escaped = htmlspecialchars( $xml );
        error_log("MWArchimate: Escaped XML = " . $escaped);
        $parser->getOutput()->addModules( [ 'ext.mwarchimate' ] );

        return '<div class="mwarchimate-container" data-xml="' . $escaped . '"></div>';
    }
}
