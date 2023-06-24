<tr>
    <td><?php echo $this->formLabel($this->element->getName(), $this->element->getLabel()) ?></td>
    <td><img src="http://placehold.it/16x16" alt="Home_icon" width="16" height="16"></td>
    <td>
        <?php echo $this->{$this->element->helper}($this->element->getName(), $this->element->getValue(),
                $this->element->getAttribs(), $this->element->getMultiOptions()
        ) ?>
        <div class="description"><?php echo $this->element->getDescription() ?></div>
        <div><?php echo $this->formErrors($this->element->getMessages()) ?></div>
    </td>
</tr>