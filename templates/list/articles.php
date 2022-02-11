  <div class="container">
    <article id="article-container">
      <h1 class="title">Article list</h1>

      <form id="search-tag" action="./articles" method="POST">
        <label for="tags-choice">Filter by tag:</label>
        <input list="article-tags" id="tag-choice" name="tags-choice" />

        <datalist id="article-tags">
          <?php foreach ($this->getAllTags() as $tag) { ?>
            <option value=<?php echo $tag; ?>>
          <?php }?>
          <option value="all"></option>
        </datalist>
        <input type="submit" name="submit" value="Submit"/>
      </form>

      <hr>  
      <!-- ARTICLES -->
      <table id="article-list">
        <tbody id="article-body">
          <?php foreach ($this->articles as $key=>$value) { ?>
          <tr>
            <td id="name"><?php echo $value['name']; ?></td>
            
            <td id="list-tags">
              <?php 
                $split = explode(' ', $value['tag']);
                if (sizeof($split) == 1) { ?>
                  <span class="badge rounded-pill bg-success"><?php echo $split[0];?>
                <?php } else if (sizeof($split) > 1) { 
                  foreach ($split as $tag) { ?>
                    <span class="badge rounded-pill bg-success"><?php echo $tag;?> </span>
                  <?php }
                }?>
            </td>
            <td><a id="show" href=<?php echo "./article/$key"?>>Show</a></td>
            <td><a id="edit" href=<?php echo "./article-edit/$key"?>>Edit</a></td>
            <td><a id= <?php echo "$key" ?> class="delete">Delete</a></td>
          </tr>
          <?php }?>
        </tbody>  
      </table>

      <hr>

      <!-- BUTTONS -->
      <div class="button-container">
        <div class="left-dialog">
          <button class="button" id="button-previous">
            Previous
          </button>
          <button class="button" id="button-next">
            Next
          </button>
          <p id="page-count">Page: </p>
        </div>

        <div class="right-dialog">
          <button class="button bigger-button" id="button-create-article">Create article</button>
        </div>
      </div>

    </article>

    <div id="dialog" class="dialog-container" hidden>
      
      <form action="./articles" method="POST">
        <label for="title-name-create">Title name:</label><br>

        <textarea type="text" name="title-name-create" 
          id="article-title" cols="32" rows="1" maxlength="32" placeholder="Enter name.."></textarea><br>

        <div id="button-container-dialog">
          <input type="submit" class="button" id="create" name="create" value="Create" disabled>
          
          <div class="button bigger-button linker">
              <a class="anchor-button" href="./articles">Cancel</a>
          </div>
          
        </div>
      </form>

    </div>
  </div>

  <script src="./templates/list/articles.js"></script>