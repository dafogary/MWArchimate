<?php

class SpecialUploadMWArchimate extends SpecialPage {
    public function __construct() {
        parent::__construct( 'UploadMWArchimate' );
    }

    public function execute( $subPage ) {
        $this->setHeaders();
        $out = $this->getOutput();
        $request = $this->getRequest();

        if ( $request->wasPosted() && $request->getFileTempname( 'archixml' ) ) {
            $file = $request->getUpload( 'archixml' );
            $filename = basename( $file['name'] );
            $dest = __DIR__ . '/../uploads/' . $filename;

            if ( move_uploaded_file( $file['tmp_name'], $dest ) ) {
                $out->addHTML( "<p>Upload successful. Use: <code>&lt;mwarchimate file=\"$filename\" /&gt;</code></p>" );
            } else {
                $out->addHTML( "<p>Upload failed</p>" );
            }
        }

        $form = <<<HTML
<form method="POST" enctype="multipart/form-data">
    <label>Upload ArchiMate XML:</label><br>
    <input type="file" name="archixml" accept=".xml"><br><br>
    <input type="submit" value="Upload">
</form>
HTML;

        $out->addHTML( $form );
    }
}