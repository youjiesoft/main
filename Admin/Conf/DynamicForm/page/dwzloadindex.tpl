<volist id="vo" name="list" key="key2">
	<tr target="sid_node" rel="{$vo['id']}"  data-tool='{$vo[classarr]}'>
		<td class="tml-first-td">{$numPerPage*($currentPage-1)+$key+1}</td>
		<volist id="vo1" name="detailList">
			<if condition="$vo1[shows] eq 1">
				<td width="{$vo1[widths]}">
					<if condition="count($vo1['func']) gt 0">
						<volist name="vo1.func" id="nam">
							<if condition="!empty($vo1['extention_html_start'][$key])">{$vo1['extention_html_start'][$key]}</if>
								{:getConfigFunction($vo[$vo1['name']],$nam,$vo1['funcdata'][$key],$list[$key2-1])}
							<if condition="!empty($vo1['extention_html_end'][$key])">{$vo1['extention_html_end'][$key]}</if>
						</volist>
					<else />
						{$vo[$vo1['name']]}
					</if>
				</td>
			</if>
		</volist>
	</tr>
</volist> 