<?php
	
class Auction extends Resource
{
	protected $region;
	
	protected $methods_allowed = array(
		'auction'
	);

	public function uncompress( $srcFileName, $dstFileName, $fileSize )
	{
		// getting content of the compressed file
		$zp = gzopen( $srcFileName, "r" );
		$data = fread ( $zp, $fileSize );
		gzclose( $zp );
		
		// writing uncompressed file
		$fp = fopen( $dstFileName, "w" );
		fwrite( $fp, $data );
		fclose( $fp );
	}
	
	public function GetAuction($realm)
	{
		//compress( "tmp/supportkonzept.rtf", "tmp/_supportkonzept.rtf.gz" );
		//uncompress( "tmp/_supportkonzept.rtf.gz", "tmp/_supportkonzept.rtf", filesize( "tmp/supportkonzept.rtf" ) );
		if (empty($realm)) {
			throw new ResourceException('No realms specified.');
		}
		else 
		{
			$data = $this->consume('auction', array(
			'server' => $realm,
			'type' => 'GET',
			'header'=>"Accept-language: ".$this->region."\r\nContent-Type: text/html; charset=UTF-8"
			));
		}
		return $data;
		
	}
	
	






}

?>