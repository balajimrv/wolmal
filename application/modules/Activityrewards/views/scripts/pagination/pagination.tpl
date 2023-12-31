
<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">

    <?php /* Previous page link */ ?>
    <?php if (isset($this->previous)): ?>
     <li><a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $this->previous;?>')"><?php echo $this->translate("&#171; Previous") ?></a></li>
    <?php endif; ?>

    <?php foreach ($this->pagesInRange as $page): ?>
      <?php if ($page != $this->current): ?>
        <li><a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $page;?>')"><?php echo $page;?></a> </li>
      <?php else: ?>
        <li class="selected"><a href="javascript:void(0)"><?php echo $page; ?></a></li>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php /* Next page link */ ?>
    <?php if (isset($this->next)): ?>
        <li><a href="javascript:void(0)" onclick="javascript:pageAction('<?php echo $this->next;?>')"><?php echo $this->translate("Next &#187;") ?></a></li>
    <?php endif; ?>

  </ul>
<?php endif; ?>