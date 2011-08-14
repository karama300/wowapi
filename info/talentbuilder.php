<?php
/*
*
*	Ulminias Talent builder for blizzard api
*
*	this is a modified version of the wowroster talent builder 
*	of corse wecannot use roster core functions here so we use a 
*	older database file include wowdb.php on the page your gonna use 
*	to call this data... 
*	
*
*
*
*/

class talents {

	function show_talents( $class ,$builddata)
	{
		global $wowdb, $addon;

		/*
			on my site we use a stored value for full build strings but you can pass live values too...
			thats what this script is gona do modafi to your hearts content!
			
		$sqlquery2 = "SELECT * FROM `api_talent_builds` WHERE `member_id` = '$this->memberID'";
		$result2 = $wowdb->query($sqlquery2);
		$talents = array();
		while($row = $wowdb->fetch($result2))
		{
			$talents[$row['build']] = $row['tree'];//'';
		}
		sort($talents);
		*/
		$e = array();
		$tree_rows = count($talents);
		$trees = $this->build_talenttree_data($class);

		// Talent data and build spec data
		$talentdata = $specdata = array();
		$build='1';

		// Temp var for talent spec detection
		$spec_points_temp = array();

		//foreach( $talents as $build => $builddata )
		//{
			$spc = '1';// we set this to 1 .. because we are passing 1 get it hehe #1...$build;
			$ts = $this->_talent_layer2($builddata,$class);
			foreach( $ts as $tree => $data )
			{
				$order = $data['order'];
				if( !isset($spec_points_temp[$build]) )
				{
					$spec_points_temp[$build] = $data['spent'];
					$specdata[$build]['order'] = $build;
					$specdata[$build]['name'] = $tree;
					$specdata[$build]['icon'] = $data['background'];
				}
				elseif( $data['spent'] > $spec_points_temp[$build] )
				{
					$specdata[$build]['order'] = $data['order'];
					$specdata[$build]['name'] = $tree;
					$specdata[$build]['icon'] = $data['background'];

					// Store highest tree points to temp var
					$spec_points_temp[$build] = $data['spent'];
				}

				// Store our talent points for later use
				$specdata[$build]['points'][$order] = $data['spent'];

				// Set talent tree data
				$talentdata[$build][$order]['name'] = $tree;
				$talentdata[$build][$order]['image'] = $data['background'];
				$talentdata[$build][$order]['points'] = $data['spent'];
				$talentdata[$build][$order]['talents'] = $data;
			}

			$e['talent']= array(
				'ID'    => $build,
				'NAME'  => $specdata[$build]['name'],
				'TYPE'  => 'Active',
				'BUILD' => implode(' / ', $specdata[$build]['points']),
				'ICON'  => $specdata[$build]['icon'],
				'SELECTED' => ($build == 0 ? true : false)
				);
                        //aprint($talentdata);
			foreach( $talentdata as $build => $builddata )
			{
				if( $spc == $build )
				{
					// Loop trees in build
					foreach( $builddata as $treeindex => $tree )
					{
						$e['talent'][$treeindex] = array(
							'L_POINTS_SPENT' => $tree['name'].' Points Spent',
							'NAME' => $tree['name'],
							'ID' => $treeindex,
							'POINTS' => $tree['points'],
							'ICON' => $tree['image'],
							'SELECTED' => ($spc == $build ? true : false)
							);

						foreach( $tree['talents'] as $row )
						{
							if( is_array($row) )
							{

								foreach( $row as $cell )
								{
								if( isset($cell['row']) )
									{
									$e['talent'][$treeindex][$cell['column']][$cell['row']]= array(
										'NAME'      => $cell['name'],
										'RANK'      => (isset($cell['rank']) ? $cell['rank'] : 0),
										'MAXRANK'   => (isset($cell['maxrank']) ? $cell['maxrank'] : 0),
										'TOOLTIP'   => (isset($cell['tooltip']) ? $cell['tooltip'] : ''),
										'ICON'      => (isset($cell['image']) ? $cell['image'] : ''),

										'S_MAX'     => (isset($cell['rank']) && $cell['rank'] == $cell['maxrank'] ? true : false),
										'S_ABILITY' => false,
										);
									}
								}
							}
						}
					}
				}
			}
		//}

		return $e;
	}

	function build_talent_data( $class )
	{
		global $wowdb, $addon;
		$sql = "SELECT * FROM `api_talents_data`"
			. " WHERE `class_id` = '" . $class . "'"
			. " ORDER BY `tree_order` ASC, `row` ASC , `column` ASC;";

		$t = array();
		$results = $wowdb->query($sql);

		$is = '';
		$ii = '';
		if( $results && $wowdb->num_rows($results) > 0 )
		{
			while( $row = $wowdb->fetch($results, SQL_ASSOC) )
			{
				$is++;
				$ii++;
				$t[$row['tree']][$row['row']][$row['column']]['name'] = $row['name'];
				$t[$row['tree']][$row['row']][$row['column']]['id'] = $row['talent_id'];
				$t[$row['tree']][$row['row']][$row['column']]['tooltip'][$row['rank']] = $row['tooltip'];
				$t[$row['tree']][$row['row']][$row['column']]['icon'] = $row['texture'];
			}
		}
		return $t;
	}

	function _talent_layer2( $build, $class )
	{
		global $wowdb;

		$sqlquery = "SELECT * FROM `api_talenttree_data` WHERE `class_id` = '" . $class . "';";
		$result = $wowdb->query($sqlquery);

		$treed = array();
		while( $row = $wowdb->fetch($result) )
		{
			$treed[$row['tree']]['background'] = $row['background'];
			$treed[$row['tree']]['icon'] = $row['icon'];
			$treed[$row['tree']]['order'] = $row['order'];
		}
		
		$talentinfo = $this->build_talent_data($class);
		$returndata = array();
		$talentArray = preg_split('//', $build, -1, PREG_SPLIT_NO_EMPTY);
		$i = 0;
		$t = 0;
		$n = '';
		$spent = 0;
		$dd = 0;
		foreach( $talentinfo as $ti => $talentdata )
		{
			for( $r = 1; $r < 7 + 1; $r++ )
			{
				for( $c = 1; $c < 4 + 1; $c++ )
				{
					$dd++;
					$returndata[$ti][$r][$c]['name'] = '';
					$returndata[$ti][$r][$c]['num'] = $dd;
				}
			}
			$spent = '';
			$returndata[$ti]['icon'] = $treed[$ti]['icon'];
			$returndata[$ti]['background'] = $treed[$ti]['background'];
			$returndata[$ti]['order'] = $treed[$ti]['order'];

			foreach( $talentdata as $c => $cdata )
			{
				$maxrank = 0;
				foreach( $cdata as $r => $rdata )
				{

					$max = count($rdata['tooltip']);
					$returndata[$ti][$c][$r]['name'] = $rdata['name'];
					$returndata[$ti][$c][$r]['rank'] = $talentArray[$i];
					$returndata[$ti][$c][$r]['maxrank'] = count($rdata['tooltip']);
					$returndata[$ti][$c][$r]['row'] = $r;
					$returndata[$ti][$c][$r]['column'] = $c;
					$returndata[$ti][$c][$r]['image'] = $rdata['icon'] . '.png';
					$rank = '';
					if (isset($talentArray[$i]) && $talentArray[$i] != 0)
					{
						$rannk = $talentArray[$i];
					}
					elseif ($talentArray[$i] == 0)
					{
						$rannk = 1;
					}
					else
					{
						$rannk = 1;
					}

					// Detect max rank and set color
					if ($talentArray[$i] < $max)
					{
						$maxc = '#6FFF5B';
					}
					else
					{
						$maxc = '#FFD200';
					}

					$tooltipp = $rdata['tooltip'][$rannk];
					$tp = '<div style="color:' . $maxc . ';font-weight:bold">rank: ' . $talentArray[$i] . ' / ' . $max . '</div><div>' . $tooltipp . '</div>';
					$returndata[$ti][$c][$r]['ttip'] = '';//$tooltipp;
					$returndata[$ti][$c][$r]['tooltip'] = '';//$tp;

					$spent = ($spent + $talentArray[$i]);
					if( $rdata['name'] != $n )
					{
						$i++;
					}
					$n = $rdata['name'];
				}
			}
			$returndata[$ti]['spent'] = $spent;

			$t++;
		}
		return $returndata;
	}
	
	
	function build_talenttree_data( $class )
	{
		global $wowdb;
		$sql = "SELECT * FROM `api_talenttree_data`"
			. " WHERE `class_id` = '" . $class . "'"
			. " ORDER BY `order` ASC;";

		$t = array();
		$results = $wowdb->query($sql);
		$is = '';
		$ii = '';

		if( $results && $wowdb->num_rows($results) > 0 )
		{
			while( $row = $wowdb->fetch($results, SQL_ASSOC) )
			{
				$is++;
				$ii++;
				$t[$row['tree']]['name'] = $row['tree'];
				$t[$row['tree']]['background'] = $row['background'];
				$t[$row['tree']]['icon'] = $row['icon'];
				$t[$row['tree']]['order'] = $row['order'];
			}
		}
		return $t;

	}

}


?>