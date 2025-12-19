<?php
echo '<?xml version="1.0" encoding="'.$enc.'"?>';
?>
<rss version="2.0" >
	<channel><?php 
	foreach($xml_data as $key => $val) {
		if(is_array($val)) {
			if(!is_numeric($key)) {
				$pre_content = "\n";
				for($i = 0; $i < 2; $i++) 
					$pre_content .= "\t";
				$pre_content .= "<" . $key . ">";
				$post_content = "\n";
				for($i = 0; $i < 2; $i++) 
					$post_content .= "\t";
				$post_content .= "</" . $key . ">";
				
				gen_xml_data($val, 3, $pre_content, $post_content);
			}
		} else {
			gen_xml_data(array($key => $val), 2);
		}
	}
	?>
	
	</channel>
</rss>
