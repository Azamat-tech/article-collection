<div class="container">
    <form id="edit-form" action=<?php echo "../article-edit/$this->id" ?> method="POST">
        <label for="title-name-edit">Name</label>

        <div class="name-wrapper"> 
            <textarea type="text" id="name-wrapper" name="title-name-edit" rows="1" 
                cols="32" maxlength="32" placeholder="Enter name.." required><?php 
                    echo htmlspecialchars($this->getTitle()); 
                ?></textarea><br>
        </div>
        
        <label for="content">Content</label>

        <textarea id="content-wrapper" type="text" name="content" rows="16" cols="64" maxlength="1024" placeholder="Write here.."><?php
                echo htmlspecialchars($this->getContent()); 
            ?></textarea><br>

        <label for="tags">Tags</label><br>

        <textarea name="tags" id="article-tags" cols="30" rows="1"><?php echo $this->getCurrentUsersTags();?></textarea><br>

        <div class="button-container">
            <input type="submit" id="save-button" name="save" class="button" value="Save" >
            <div id="edit-back-to-articles" class="button bigger-button back linker">
                <a class="anchor-button" href="../articles">Back to Articles</button>
            </div>
        </div>


    </article>
</div>