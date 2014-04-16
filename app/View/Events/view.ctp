<?php
$mayModify = (($isAclModify && $event['Event']['user_id'] == $me['id'] && $event['Event']['orgc'] == $me['org']) || ($isAclModifyOrg && $event['Event']['orgc'] == $me['org']));
$mayPublish = ($isAclPublish && $event['Event']['orgc'] == $me['org']);
?>
<?php
	echo $this->Html->script('ajaxification');
	echo $this->element('side_menu', array('menuList' => 'event', 'menuItem' => 'viewEvent', 'mayModify' => $mayModify, 'mayPublish' => $mayPublish));
?>
<div class="events view">
	<?php
		if ('true' == Configure::read('MISP.showorg') || $isAdmin) {
			echo $this->element('img', array('id' => $event['Event']['orgc']));
			$left = true;
		}
		$title = $event['Event']['info'];
		if (strlen($title) > 55) $title = substr($title, 0, 55) . '...';
	?>
	<div class="row-fluid">
		<div class="span8">
			<h2><?php echo nl2br(h($title)); ?></h2>
			<dl>
				<dt>Event ID</dt>
				<dd>
					<?php echo h($event['Event']['id']); ?>
					&nbsp;
				</dd>
				<dt>Uuid</dt>
				<dd>
					<?php echo h($event['Event']['uuid']); ?>
					&nbsp;
				</dd>
				<?php if ('true' == Configure::read('MISP.showorg') || $isAdmin): ?>
				<dt>Org</dt>
				<dd>
					<?php echo h($event['Event']['orgc']); ?>
					&nbsp;
				</dd>
				<?php endif; ?>
				<?php if ($isSiteAdmin): ?>
				<dt>Owner org</dt>
				<dd>
					<?php echo h($event['Event']['org']); ?>
					&nbsp;
				</dd>
				<?php endif; ?>
				<dt>Contributors</dt>
				<dd>
					<?php 
						foreach($logEntries as $k => $entry) {
							if ('true' == Configure::read('MISP.showorg') || $isAdmin) {
								?>
									<a href="/logs/event_index/<?php echo $event['Event']['id'] . '/' . h($entry['Log']['org']);?>" style="margin-right:2px;text-decoration: none;">
								<?php 
									echo $this->element('img', array('id' => $entry['Log']['org'], 'imgSize' => 24, 'imgStyle' => true));
								?>
									</a>
								<?php 
							}
						}		
					?>
					&nbsp;
				</dd>
				<?php if (isset($event['User']['email']) && ($isSiteAdmin || ($isAdmin && $me['org'] == $event['Event']['org']))): ?>
				<dt>Email</dt>
				<dd>
					<?php echo h($event['User']['email']); ?>
					&nbsp;
				</dd>
				<?php endif; ?>
				<?php 
					if (Configure::read('MISP.tagging')): ?>
						<dt>Tags</dt>
						<dd>
						<table>
							<tr>
						<?php 
							foreach ($tags as $tag): ?>
							<td style="padding-right:0px;">
								<?php if ($isAclTagger): ?>
								<a href="/events/index/searchtag:<?php echo $tag['Tag']['id']; ?>" class=tagFirstHalf style="background-color:<?php echo $tag['Tag']['colour'];?>;color:<?php echo $this->TextColour->getTextColour($tag['Tag']['colour']);?>"><?php echo h($tag['Tag']['name']); ?></a>
								<?php else: ?>
								<a href="/events/index/searchtag:<?php echo $tag['Tag']['id']; ?>" class=tag style="background-color:<?php echo $tag['Tag']['colour'];?>;color:<?php echo $this->TextColour->getTextColour($tag['Tag']['colour']);?>"><?php echo h($tag['Tag']['name']); ?></a>
								<?php endif; ?>
							</td>
							<?php if ($isAclTagger): ?>
							<td style="padding-left:0px;padding-right:5px;">
							<?php 
							echo $this->Form->postLink('x', array('action' => 'removeTag', $event['Event']['id'], $tag['Tag']['id']), array('class' => 'tagSecondHalf', 'title' => 'Delete'), ('Are you sure you want to delete this tag?'));
							?>
							</td>
							<?php endif; ?>
							<?php 
							endforeach;
							if ($isAclTagger) : ?>
							<td id ="addTagTD" style="display:none;">
								<?php
									echo $this->Form->create('', array('action' => 'addTag', 'style' => 'margin:0px;'));
									echo $this->Form->hidden('id', array('value' => $event['Event']['id']));
									echo $this->Form->input('tag', array(
										'options' => array($allTags),
										'value' => 0,
										'label' => false,
										'style' => array('height:22px;padding:0px;margin-bottom:0px;'),
										'onChange' => 'this.form.submit()',
										'class' => 'input-large'));
									echo $this->Form->end();
								?>
							</td>
							<td>
							<button id="addTagButton" class="btn btn-inverse" style="line-height:10px; padding: 4px 4px;">+</button>
					
							</td>
							<?php else:
									if (empty($tags)) echo '&nbsp;'; 
								endif; ?>
						</tr>
						</table>
						</dd>
				<?php endif; ?>
				<dt>Date</dt>
				<dd>
					<?php echo h($event['Event']['date']); ?>
					&nbsp;
				</dd>
				<dt title="<?php echo $eventDescriptions['threat_level_id']['desc'];?>">Threat Level</dt>
				<dd>
					<?php 
						if ($event['ThreatLevel']['name']) echo h($event['ThreatLevel']['name']);
						else echo h($event['Event']['threat_level_id']);
					?>
					&nbsp;
				</dd>
				<dt title="<?php echo $eventDescriptions['analysis']['desc'];?>">Analysis</dt>
				<dd>
					<?php echo h($analysisLevels[$event['Event']['analysis']]); ?>
					&nbsp;
				</dd>
				<dt>Distribution</dt>
				<dd <?php if($event['Event']['distribution'] == 0) echo 'class = "privateRedText"';?> title = "<?php echo h($distributionDescriptions[$event['Event']['distribution']]['formdesc'])?>">
					<?php 
						echo h($distributionLevels[$event['Event']['distribution']]); 
					?>
				</dd>
				<dt>Description</dt>
				<dd>
					<?php echo nl2br(h($event['Event']['info'])); ?>
					&nbsp;
				</dd>
				<dt>Published</dt>
				<dd style="color: red;">
					<b><?php echo ($event['Event']['published'] == 1 ? 'Yes' : 'No');  ?></b>
					&nbsp;
				</dd>
			</dl>
		</div>

	<?php if (!empty($relatedEvents)):?>
	<div class="related span4">
		<h3>Related Events</h3>
		<ul class="inline">
			<?php foreach ($relatedEvents as $relatedEvent): ?>
			<li>
			<div title="<?php echo h($relatedEvent['Event']['info']); ?>">
			<?php
			$linkText = $relatedEvent['Event']['date'] . ' (' . $relatedEvent['Event']['id'] . ')';
			if ($relatedEvent['Event']['org'] == $me['org']) {
				echo $this->Html->link($linkText, array('controller' => 'events', 'action' => 'view', $relatedEvent['Event']['id'], true, $event['Event']['id']), array('style' => 'color:red;'));
			} else {
				echo $this->Html->link($linkText, array('controller' => 'events', 'action' => 'view', $relatedEvent['Event']['id'], true, $event['Event']['id']));
			}
			?>
			</div></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	</div>
	<br />
	<div class="toggleButtons">
		<button class="btn btn-inverse toggle-left btn.active qet" id="pivots_active">
			<span class="icon-minus icon-white" style="vertical-align:top;"></span>Pivots
		</button>
		<button class="btn btn-inverse toggle-left qet" style="display:none;" id="pivots_inactive">
			<span class="icon-plus icon-white" style="vertical-align:top;"></span>Pivots
		</button>
		<button class="btn btn-inverse toggle qet" id="attributes_active">
			<span class="icon-minus icon-white" style="vertical-align:top;"></span>Attributes
		</button>
		<button class="btn btn-inverse toggle qet" id="attributes_inactive" style="display:none;">
			<span class="icon-plus icon-white" style="vertical-align:top;"></span>Attributes
		</button>
		<button class="btn btn-inverse toggle-right qet" id="discussions_active">
			<span class="icon-minus icon-white" style="vertical-align:top;"></span>Discussion
		</button>
		<button class="btn btn-inverse toggle-right qet" id="discussions_inactive" style="display:none;">
			<span class="icon-plus icon-white" style="vertical-align:top;"></span>Discussion
		</button>
	</div>
	<br />
	<br />
	<div id="pivots_div">
		<?php if (sizeOf($allPivots) > 1) echo $this->element('pivot'); ?>
	</div>
	<div id="attribute_add_form" class="attribute_add_form"></div>
	<div id="attribute_creation_div" style="display:none;">
		<?php 
			echo $this->element('eventattributecreation');
		?>
	</div>
	<div id="attributes_div">
		<?php 
			echo $this->element('eventattribute');
		?>
	</div>
	<div id="discussions_div">
		<?php
			echo $this->element('eventdiscussion');
		?>
	</div>
</div>
<script type="text/javascript">
// tooltips
$(document).ready(function () {
	$("th, td, dt, div, span, li").tooltip({
		'placement': 'top',
		'container' : 'body',
		delay: { show: 500, hide: 100 }
		});
	$('#discussions_active').click(function() {
		  $('#discussions_div').hide();
		  $('#discussions_active').hide();
		  $('#discussions_inactive').show();
		});
	$('#discussions_inactive').click(function() {
		  $('#discussions_div').show();
		  $('#discussions_active').show();
		  $('#discussions_inactive').hide();
		});
	$('#attributes_active').click(function() {
		  $('#attributes_div').hide();
		  $('#attributes_active').hide();
		  $('#attributes_inactive').show();
		});
	$('#attributes_inactive').click(function() {
		  $('#attributes_div').show();
		  $('#attributes_active').show();
		  $('#attributes_inactive').hide();
		});
	$('#pivots_active').click(function() {
		  $('#pivots_div').hide();
		  $('#pivots_active').hide();
		  $('#pivots_inactive').show();
		});
	$('#pivots_inactive').click(function() {
		  $('#pivots_div').show();
		  $('#pivots_active').show();
		  $('#pivots_inactive').hide();
		});

	$('#addTagButton').click(function() {
		$('#addTagTD').show();
		$('#addTagButton').hide();
	});
});

function clickCreateButton() {
	$.get( "/attributes/add/<?php echo $event['Event']['id']; ?>", function(data) {
		$("#attribute_add_form").show();
		$("#gray_out").show();
		$("#attribute_add_form").html(data);
	});
}
</script>