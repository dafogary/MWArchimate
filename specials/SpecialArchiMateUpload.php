<?php

use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Permissions\PermissionsError;
use MediaWiki\Title\Title;
use MediaWiki\MediaWikiServices;

class SpecialArchiMateUpload extends SpecialPage {

    public function __construct() {
        parent::__construct( 'ArchiMateUpload', 'upload' );
    }

    public function isListed() {
        return true;
    }

    public function getGroupName() {
        return 'media';
    }

    public function execute( $par ) {
        $this->setHeaders();
        $out = $this->getOutput();
        $request = $this->getRequest();
        $user = $this->getUser();

        if ( !$user->isAllowed( 'upload' ) ) {
            throw new PermissionsError( 'upload' );
        }

        if ( $request->wasPosted() && $request->getFileName( 'archimatefile' ) ) {
            // Force MIME type for .archimate files 
            $fileName = $request->getFileName('archimatefile');
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Register MIME type for ArchiMate files if not already done
            if ($fileExtension === 'archimate' && !in_array('application/xml', $GLOBALS['wgFileExtensions'])) {
                $GLOBALS['wgMimeTypeMap']['archimate'] = 'application/xml';
            }
            
            // Use the fully qualified class name with backward compatibility
            $upload = \UploadBase::createFromRequest($request, 'archimatefile');
            
            // Check if upload object was created successfully
            if (!$upload) {
                $out->addWikiTextAsInterface("'''Upload failed:''' File type not allowed or upload error.");
                
                // Create a custom upload handler as fallback
                try {
                    // Get the temporary file
                    $tmpName = $request->getUpload('archimatefile')->getTempName();
                    $destName = $request->getText('dest') ?: $fileName;
                    
                    // Verify that the file exists and has content before proceeding
                    if (file_exists($tmpName) && filesize($tmpName) > 0) {
                        $out->addHTML("<p>Temp file exists and has size: " . filesize($tmpName) . " bytes</p>");
                        
                        // Manually set up upload process using core methods
                        $upload = new UploadFromFile();
                        
                        // Correct way to initialize UploadFromFile
                        $upload->initialize(
                            $request->getFileName('archimatefile'),
                            $request->getUpload('archimatefile'), // Pass the WebRequestUpload object directly
                            filesize($tmpName)
                        );
                        
                        if (method_exists($upload, 'setDesiredDestName') && $destName) {
                            $upload->setDesiredDestName($destName);
                        }
                        
                        $out->addHTML("<p>Attempting alternative upload method...</p>");
                    } else {
                        $out->addHTML("<p>Temporary file is missing or empty: " . $tmpName . "</p>");
                        $upload = null;
                    }
                } catch (Exception $e) {
                    $out->addHTML("<p>Alternative upload failed: " . htmlspecialchars($e->getMessage()) . "</p>");
                    $upload = null;
                }
            }
            
            if ($upload) {
                // Set upload properties
                $comment = 'Uploaded ArchiMate model via Special:ArchiMateUpload';
                
                // Optional: Use the destination name if provided
                $destName = $request->getText('dest');
                if ($destName) {
                    // Only try to set destination if method exists
                    if (method_exists($upload, 'setDesiredDestName')) {
                        $upload->setDesiredDestName($destName);
                    }
                }
                
                // Verify the upload first
                $verifyStatus = $upload->verifyUpload();
                if ($verifyStatus['status'] !== UploadBase::OK) {
                    $out->addWikiTextAsInterface("'''Upload verification failed:''' " . 
                        $this->getErrorMessage($verifyStatus));
                } else {
                    // Perform the actual upload
                    $status = $upload->performUpload(
                        $comment,
                        false, // No watch
                        false, // No explicit verification
                        $user
                    );

                    if ( $status->isGood() ) {
                        $title = $upload->getTitle();
                        $link = $this->getLinkRenderer()->makeLink( $title );
                        $out->addWikiTextAsInterface( "'''Upload successful!''' File: $link" );
                    } else {
                        $out->addWikiTextAsInterface( "'''Upload failed:''' " . $status->getWikiText() );
                    }
                }
            }

            // Add extensive debug output
            $out->addHTML('<pre>');
            $out->addHTML("MediaWiki version: " . MW_VERSION . "\n");
            $out->addHTML("File name: " . htmlspecialchars($request->getFileName('archimatefile')) . "\n");
            $out->addHTML("Temp name: " . htmlspecialchars($request->getUpload('archimatefile')->getTempName()) . "\n");
            $out->addHTML("File type: " . htmlspecialchars($request->getUpload('archimatefile')->getType()) . "\n");
            $out->addHTML("File extension: " . htmlspecialchars($fileExtension) . "\n");
            $out->addHTML("File size: " . htmlspecialchars($request->getUpload('archimatefile')->getSize()) . "\n");
            $out->addHTML("Allowed extensions: " . print_r($GLOBALS['wgFileExtensions'], true) . "\n");
            $out->addHTML("MIME exclusions: " . print_r($GLOBALS['wgMimeTypeExclusions'], true) . "\n"); 
            $out->addHTML("Check file extensions: " . ($GLOBALS['wgCheckFileExtensions'] ? 'true' : 'false') . "\n");
            $out->addHTML("Verify MIME type: " . ($GLOBALS['wgVerifyMimeType'] ? 'true' : 'false') . "\n");
            $out->addHTML('</pre>');
        }

        // Show the form
        $out->addHTML( '
            <h2>Upload ArchiMate Model</h2>
            <form method="post" enctype="multipart/form-data">
                <p><label>.archimate file:
                    <input type="file" name="archimatefile" accept=".archimate,.xml" required>
                </label></p>
                <p><label>Destination filename (optional):
                    <input type="text" name="dest">
                </label></p>
                <p><input type="submit" value="Upload"></p>
            </form>
        ' );
    }

    /**
     * Get human-readable error message for upload errors
     */
    private function getErrorMessage($verifyStatus) {
        switch ($verifyStatus['status']) {
            case UploadBase::EMPTY_FILE:
                return 'The file is empty.';
            case UploadBase::FILE_TOO_LARGE:
                return 'The file is too large.';
            case UploadBase::FILETYPE_MISSING:
                return 'The file is missing an extension.';
            case UploadBase::FILETYPE_BADTYPE:
                return 'This type of file is not allowed.';
            case UploadBase::VERIFICATION_ERROR:
                return $verifyStatus['details'][0];
            default:
                return 'Unknown error.';
        }
    }
}