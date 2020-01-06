<?php
/*
 * @copyright  Creative Spirits (c) 2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

echo<<<EOT
<style>
h3.header_level {
	border-bottom: 1px solid;
	border-top: 1px solid;
	border-width:thin;
	padding-bottom: 3px;
	padding-left: 3px;
}
div.item_sep {
	border-top: 1px solid;
	padding-top: 7px;
}
td.desc_sep {
	padding-top: 7px;
	padding-bottom: 7px;
}
</style>

EOT;
// todo: do this in a template-independent way
echo<<<EOT
<div class="page-header">
	<h2 itemprop="headline">Sponsors</h2>
</div>
EOT;

$intro_text = JComponentHelper::getParams('com_cs_sponsors')->get("intro_text",null);

if ( $intro_text !== null)
	echo "<p>$intro_text</p>";

echo "<div>";

echo showSponsors();

echo "</div>";

function showSponsors()
{
	// headers can be Charter Business Sponsors, Premier Business Sponsors and Business Sponsors 

	$where = "WHERE sponsor_level!=0";
	$order = "ORDER BY sponsor_level ASC";
	$sql = "SELECT typ FROM #__cs_members_types $where $order";
	$db = JFactory::getDBO();
	$db->setQuery($sql);
	$typs = $db->loadAssocList();

	$header_colors = JComponentHelper::getParams('com_cs_sponsors')->get("header_colors",null);
	if ( $header_colors === null)
		$header_colors =  array("Goldenrod");	// todo: could be a component default
	else
	{
		$ntyps = count($typs);
		$header_colors = explode(",", $header_colors);
	}
	
	// create arrays of sponsorship level types and their respective header colors
	// eg, array("Charter Business", "Premier Business", "Business")
	
	$levels = array();
	$nlvl = 0; 	
	foreach($typs as $typ)
	{
		$levels[] = $typ["typ"];
		
		// component param for "Gold,Silver,Bronze": Goldenrod,Silver,Palegoldenrod
		
		$color = isset( $header_colors[$nlvl]) ? $header_colors[$nlvl] : $header_colors[0];
		$colors[$typ["typ"]] = $color;
		$nlvl++;
	}

	// for each sponsorship level, output a header and the sponsors in the level if any
	
	$ret = "";
	$today = date('Y-m-d');	// outputs: 2018-12-20
	
	foreach( $levels as $level )
	{
		// find all the paid up active business members in the level
		
		$where = "WHERE status='Active' AND memtype='$level' AND paidthru>='$today'";
		$order = "ORDER BY bname ASC";
		$sql = "SELECT * FROM #__cs_members $where $order";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$sponsors = $db->loadAssocList();
		
		if ( count( $sponsors ) == 0 )
			continue;

		// sponsor listing images are stored in this folder (eg, images/site/sponsors)

		$images_path = JComponentHelper::getParams('com_cs_sponsors')->get("images_path","images");
		
		// track the number of sponsors listed in the section to know when to output a level header or separator
		
		$nshown_level = 0;
		
		foreach( $sponsors as $sponsor )
		{
			// find the business member's sponsor listing
			
			$memid = $sponsor["id"];
			$where = "WHERE memid=$memid AND show_listing='1'";
			$sql = "SELECT * FROM #__cs_businesses $where";
			$db = JFactory::getDBO();
			$db->setQuery($sql);
			$listing = $db->loadAssoc();
			if ($listing === null)
				continue;
		
			if ( ++$nshown_level == 1)
			{
				// output the appropriate level header

				$ret .= "<h3 style='background-color: " . $colors[$level] .";' class='header_level'>$level Sponsors</h3>";

				// first sponsor listed doesn't need a separator

				$item_class = "";
			}
			else 
			{
				// separate subsequent listings within a level with a separator

				$item_class = " class='item_sep'";
			}

			// format sponsor listing info from their membership data
			
			// sponsor listing information always includes business name
			$bname = $sponsor["bname"];

			$info = "<b>$bname</b><br />";

			// optional contact name with optional title
			$contact_name = "";
			if ( (!empty($sponsor["fname"])) && (!empty($sponsor["lname"])))
			{
				$mi = $sponsor["mi"];
				if ( ! empty( $mi ) )
					$mi .= ". ";
				
				$contact_name = $sponsor["fname"] . " $mi" . $sponsor["lname"];
				if ( ! empty( $sponsor["title"] ) )
					$contact_name .= ", " . $sponsor["title"];
				
				$info .= "$contact_name</br />";
			}

			// add more member fields to the information listing but exclude those that are unwanted by the sponsor
			$exclude_fields = explode(',', $listing["exclude_fields"]);

			if ( (!in_array("address",$exclude_fields)) && (!empty($sponsor["address"])) && (!empty($sponsor["city"])) 
				&& (!empty($sponsor["state"])) && (!empty($sponsor["zip"])))
				$info .= sprintf("%s<br />%s, %s %s<br />", 
					$sponsor["address"], $sponsor["city"], $sponsor["state"], $sponsor["zip"] );				

			if ( (!in_array("wphone",$exclude_fields)) && (!empty($sponsor["wphone"])))
					$info .= sprintf("Phone: %s<br />",
						$sponsor["wphone"] );
			
			if ( (!in_array("cphone",$exclude_fields)) && (!empty($sponsor["cphone"])))
				$info .= sprintf("Cell: %s<br />",
					$sponsor["cphone"] );
		
			if ( (!in_array("fax",$exclude_fields)) && (!empty($sponsor["fax"])))
				$info .= sprintf("Fax: %s<br />",
					$sponsor["fax"] );
			
			if ( (!in_array("email",$exclude_fields)) && (!empty($sponsor["email"])))
				$info .= sprintf("Email: <a href='mailto:%s'>%s</a><br />",
						$sponsor["email"], $sponsor["email"] );

			// build proper website link preserving https:// protocol if present
			$href = "";
			if ( (!in_array("website",$exclude_fields)) && (!empty($sponsor["website"])))
			{
				$website = $sponsor["website"];
				if ( strpos($website,"http://") === 0 )
				{
					$href = $website;
					$website = substr($website,7);
				}
				else if ( strpos($website,"https://") === 0 )
				{
					$href = $website;
					$website = substr($website,8);	
				}
				else
				{
					$href = "http://$website";	
				}
				
				$info .= sprintf("Web: <a href='%s' target='_blank'>%s</a><br />",
						$href, $website );
			}

			// determine if listing image exists

			$img = "";
			if ( !empty($listing["image_name"]))
			{
				$img_file = "$images_path/" . $listing["image_name"];

				if ( file_exists( $img_file ))
				{
					if (empty($href))
						$img = "<img src='/$img_file'>";
					else
						$img = "<a target='_blank' href='$href'><img src='/$img_file'></a>";
				}
			}

			$desc = $listing["listing_description"];

			// sponsor listing is formatted in a table with two rows
			// row 1: info | image
			// row 2: description

			$item_html = "<a name='$memid'></a><table><tr><td>$info</td><td>$img</td></tr><td class='desc_sep' colspan='2'>$desc</td></tr></table>";
				
			$ret .= "<div$item_class>$item_html</div>";
		}
	}

	if ( empty( $ret ) )
		$ret = "<b>Please support our mission by becoming a business sponsor.</b>";
	
	return $ret;
}
