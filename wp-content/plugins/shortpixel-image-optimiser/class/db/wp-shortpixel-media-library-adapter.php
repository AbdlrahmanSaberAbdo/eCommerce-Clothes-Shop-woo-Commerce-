<?php

class WpShortPixelMediaLbraryAdapter {
    
    //count all the processable files in media library (while limiting the results to max 10000)
    public static function countAllProcessableFiles($includePdfs = true, $maxId = PHP_INT_MAX, $minId = 0){
        global  $wpdb;
        
        $totalFiles = $mainFiles = $processedMainFiles = $processedTotalFiles = 
        $procGlossyMainFiles = $procGlossyTotalFiles = $procLossyMainFiles = $procLossyTotalFiles = $procLosslessMainFiles = $procLosslessTotalFiles = $procUndefMainFiles = $procUndefTotalFiles = $mainUnprocessedThumbs = 0;
        $filesMap = $processedFilesMap = array();
        $limit = self::getOptimalChunkSize();
        $pointer = 0;
        $filesWithErrors = array();
        
        //count all the files, main and thumbs 
        while ( 1 ) {
            $ids = self::getPostIdsChunk($minId, $maxId, $pointer, $limit);
            if($ids === null) { 
                break; //we parsed all the results
            } 
            elseif(count($ids) == 0) {
                $pointer += $limit;
                continue;
            }
                        
            $filesList= $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "postmeta
                                        WHERE post_id IN (" . implode(',', $ids) . ")
                                          AND ( meta_key = '_wp_attached_file' OR meta_key = '_wp_attachment_metadata' )");
             
            foreach ( $filesList as $file ) 
            {
                if ( $file->meta_key == "_wp_attached_file" )
                {//count pdf files only
                    $extension = substr($file->meta_value, strrpos($file->meta_value,".") + 1 );
                    if ( $extension == "pdf" && !isset($filesMap[$file->meta_value]))
                    {
                        $totalFiles++;
                        $mainFiles++;
                        $filesMap[$file->meta_value] = 1;
                    }
                }
                else //_wp_attachment_metadata
                {
                    $attachment = unserialize($file->meta_value);
                    $sizesCount = isset($attachment['sizes']) ? WpShortPixelMediaLbraryAdapter::countNonWebpSizes($attachment['sizes']) : 0;
                    //processable
                    $isProcessable = false;
                    if(isset($attachment['file']) && !isset($filesMap[$attachment['file']]) && WPShortPixel::isProcessablePath($attachment['file'])){
                        $isProcessable = true;
                        if ( isset($attachment['sizes']) ) {
                            $totalFiles += $sizesCount;            
                        }
                        if ( isset($attachment['file']) )
                        {
                            $totalFiles++;
                            $mainFiles++;
                            $filesMap[$attachment['file']] = 1;
                        }
                    }
                    //processed
                    if (isset($attachment['ShortPixelImprovement'])
                        && ($attachment['ShortPixelImprovement'] > 0 || $attachment['ShortPixelImprovement'] === 0.0 || $attachment['ShortPixelImprovement'] === "0")
                        //for PDFs there is no file field so just let it pass.
                        && (!isset($attachment['file']) || !isset($processedFilesMap[$attachment['file']])) ) { 
                        
                        //add main file to counts
                        $processedMainFiles++;            
                        $processedTotalFiles++;            
                        $type = isset($attachment['ShortPixel']['type']) ? $attachment['ShortPixel']['type'] : null;
                        switch($type) {
                            case 'lossy' :
                                $procLossyMainFiles++;
                                $procLossyTotalFiles++;
                                break;
                            case 'glossy':
                                $procGlossyMainFiles++;
                                $procGlossyTotalFiles++;
                                break;
                            case 'lossless':
                                $procLosslessMainFiles++;
                                $procLosslessTotalFiles++;
                                break;
                            default:
                                $procUndefMainFiles++;
                                $procUndefTotalFiles++;
                        }
                        
                        //get the thumbs processed for that attachment
                        $thumbs = $allThumbs = 0;
                        if ( isset($attachment['ShortPixel']['thumbsOpt']) ) {
                            $thumbs = $attachment['ShortPixel']['thumbsOpt'];
                        } 
                        elseif ( isset($attachment['sizes']) ) {
                            $thumbs = $sizesCount;            
                        } 
                        $thumbsMissing = isset($attachment['ShortPixel']['thumbsMissing']) ? $attachment['ShortPixel']['thumbsMissing'] : array();

                        if ( isset($attachment['sizes']) && $sizesCount > $thumbs + count($thumbsMissing)) {
                            $mainUnprocessedThumbs++;
                        } 
                        
                        //increment with thumbs processed
                        $processedTotalFiles += $thumbs;
                        if($type == 'glossy') {
                           $procGlossyTotalFiles += $thumbs;
                        } elseif ($type == 'lossy') {
                           $procLossyTotalFiles += $thumbs;
                        } else {
                           $procLosslessTotalFiles += $thumbs;
                        }
                        
                        if ( isset($attachment['file']) ) {
                            $processedFilesMap[$attachment['file']] = 1;
                        }
                    }
                    elseif($isProcessable && isset($attachment['ShortPixelImprovement']) && count($filesWithErrors) < 50) {
                        $filePath = explode("/", $attachment["file"]);
                        $name = is_array($filePath)? $filePath[count($filePath) - 1] : $file->post_id;
                        $filesWithErrors[$file->post_id] = array('Name' => $name, 'Message' => $attachment['ShortPixelImprovement']);
                    }

                }
            }   
            unset($filesList);
            $pointer += $limit;
            
        }//end while

        return array("totalFiles" => $totalFiles, "mainFiles" => $mainFiles, 
                     "totalProcessedFiles" => $processedTotalFiles, "mainProcessedFiles" => $processedMainFiles,
                     "totalProcLossyFiles" => $procLossyTotalFiles, "mainProcLossyFiles" => $procLossyMainFiles,
                     "totalProcGlossyFiles" => $procGlossyTotalFiles, "mainProcGlossyFiles" => $procGlossyMainFiles,
                     "totalProcLosslessFiles" => $procLosslessTotalFiles, "mainProcLosslessFiles" => $procLosslessMainFiles,
                     "totalMlFiles" => $totalFiles, "mainMlFiles" => $mainFiles,
                     "totalProcessedMlFiles" => $processedTotalFiles, "mainProcessedMlFiles" => $processedMainFiles,
                     "totalProcLossyMlFiles" => $procLossyTotalFiles, "mainProcLossyMlFiles" => $procLossyMainFiles,
                     "totalProcGlossyMlFiles" => $procGlossyTotalFiles, "mainProcGlossyMlFiles" => $procGlossyMainFiles,
                     "totalProcLosslessMlFiles" => $procLosslessTotalFiles, "mainProcLosslessMlFiles" => $procLosslessMainFiles,
                     "totalProcUndefMlFiles" => $procUndefTotalFiles, "mainProcUndefMlFiles" => $procUndefMainFiles,
                     "mainUnprocessedThumbs" => $mainUnprocessedThumbs,
                     "filesWithErrors" => $filesWithErrors
                    );
    } 
    
    public static function getPostMetaSlice($startId, $endId, $limit) {
        global $wpdb;
        $queryPostMeta = "SELECT * FROM " . $wpdb->prefix . "postmeta 
            WHERE ( post_id <= $startId AND post_id >= $endId ) 
              AND ( meta_key = '_wp_attached_file' OR meta_key = '_wp_attachment_metadata' )
            ORDER BY post_id DESC
            LIMIT " . $limit;
        return $wpdb->get_results($queryPostMeta);        
    }
    
    public static function countNonWebpSizes($sizes) {
        $count = 0;
        foreach($sizes as $key => $val) {
            if (strpos($key, ShortPixelMeta::WEBP_THUMB_PREFIX) === 0) continue;
            $count++;
        }
        return $count;
    }
    
    public static function cleanupFoundThumbs($itemHandler) {
        $meta = $itemHandler->getMeta();
        $sizesAll = $meta->getThumbs();
        $sizes = array();
        $files = array();
        foreach($sizesAll as $key => $size) {
           if(strpos($key, ShortPixelMeta::FOUND_THUMB_PREFIX) === 0) continue;
           if(in_array($size['file'], $files)) continue;
           $sizes[$key] = $size;
           $files[] = $size['file'];
        }
        $meta->setThumbs($sizes);
        $itemHandler->updateMeta($meta, true);
    }
    
    public static function findThumbs($mainFile) {
        $ext = pathinfo($mainFile, PATHINFO_EXTENSION);
        $base = substr($mainFile, 0, strlen($mainFile) - strlen($ext) - 1);
        $pattern = '/' . preg_quote($base, '/') . '-\d+x\d+\.'. $ext .'/';
        $thumbsCandidates = @glob($base . "-*." . $ext);
        $thumbs = array();
        foreach($thumbsCandidates as $th) {
            if(preg_match($pattern, $th)) {
                $thumbs[]= $th;
            }
        }
        return $thumbs;
    }

    protected static function getOptimalChunkSize() {
        global  $wpdb;
        $cnt = $wpdb->get_results("SELECT count(*) posts FROM " . $wpdb->prefix . "posts");
        $posts = isset($cnt) && count($cnt) > 0 ? $cnt[0]->posts : 0;
        if($posts > 100000) {
            return 20000;
        } elseif ($posts > 50000) {
            return 5000;
        } elseif($posts > 10000) {
            return 2000;
        } else {
            return 500;
        }
    }
        
    protected static function getPostIdsChunk($minId, $maxId, $pointer, $limit) {
        global  $wpdb;
        
        $ids = array();
        $idList = $wpdb->get_results("SELECT ID, post_mime_type FROM " . $wpdb->prefix . "posts
                                    WHERE ( ID <= $maxId AND ID > $minId )
                                    LIMIT $pointer,$limit");
        if ( empty($idList) ) {
            return null;
        }
        foreach($idList as $item) {
            if($item->post_mime_type != '') {
                $ids[] = $item->ID;
            }
        }
        return $ids;
    }

}
