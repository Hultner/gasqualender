<?php
class FileAccess{
	var $Filename; //Name of the file
	var $UserPath; //Path of the file
	
	function FileAccess($path, $user){
		$this->UserPath = $path.$user.'/';
	}
	
	function VarOfFile($file, $option=FALSE){
		if($option)return unserialize(file_get_contents($file));
		else return unserialize(file_get_contents($this->UserPath.$file));
	}
	
	function FileOfVar($file, $data){
		if($fp = fopen( $this->UserPath . $file, 'w+')){
			if(fwrite($fp, serialize($data))){
				if(fclose($fp))return TRUE;
				else return FALSE;
			}else return FALSE;
		}else return FALSE;
	}
	
	function FileOfPlain($file, $data){
		if($fp = fopen( $this->UserPath . $file, 'w+')){
			if(fwrite($fp, $data)){
				if(fclose($fp))return TRUE;
				else return FALSE;
			}else return FALSE;
		}else return FALSE;
	
	}
	
	function VarOfPlain($file){
		return file_get_contents($this->UserPath.$file);
	}
	
	function deleteFile($file){
		return unlink($this->UserPath.$file);
	}
	
	function FileExist($file){
		return file_exists($this->UserPath.$file);
	}
	
	function DirCopy($source, $target) {
		if ( is_dir( $source ) ) {
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ) {
					$this->DirCopy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}
	 
			$d->close();
		}else {
			copy( $source, $target );
		}
	}
}

?>