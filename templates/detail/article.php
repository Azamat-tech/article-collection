<div class="container">
    <article>
        <h1>
            <?php echo htmlspecialchars($this->getTitle()); ?> 
        </h1>

        <?php
            $res = $this->getCurrentUsersTags();
            if (strlen($res) != 0) {?>
                <h2><b><?php echo $res; ?></b></h2>
            <?php }?>

        <hr>
        
        <p>
            <?php echo htmlspecialchars($this->getContent()); ?> 
        </p>
    
        <hr>

        <div class="button-container">
            <div id="edit-button" class="button bigger-button linker">
                <a class="anchor-button" href=<?php echo "../article-edit/$this->id"?>>Edit</a>
            </div>

            <div class="button bigger-button back linker">
                <a class="anchor-button" href="../articles">Back to Articles</button>
            </div>
        </div> 
        
    </article>
</div>
