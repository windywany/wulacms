<?xml version="1.0" encoding="UTF-8"?>
<urlset>
	{foreach $pcRootURL as $u}
	<url>
		<loc><![CDATA[{$u}]]></loc>
		<data>
			<display>
				<pc_url_pattern><![CDATA[{$u}(.*)]]></pc_url_pattern>
				<html5_url_pattern><![CDATA[{$mDomain}]]></html5_url_pattern>
				<xhtml_url_pattern><![CDATA[{$mDomain}]]></xhtml_url_pattern>
				<wml_url_pattern><![CDATA[{$mDomain}]]></wml_url_pattern>
			</display>
		</data>
	</url>
	{/foreach}
</urlset>