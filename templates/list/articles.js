let prev_button = document.getElementById("button-previous");
let next_button = document.getElementById("button-next");
let create_article_button = document.getElementById("button-create-article");

let text_input = document.getElementById("article-title");

let table = document.getElementById("article-list");

class ArticlesManager { 
    limit;
    current_page;
    total_pages;

    article_title;  // when user decides to create an article

    constructor(limit) {
        this.limit = limit;
        this.current_page = 1;
    }

    // assigns classes to the rows based on the page limit
    assignClassesToArticles() {
        console.log("STARTED");
        let temp_limit = this.limit;
        let temp_current_page = this.current_page;

        for (let i = 0, row; row = table.rows[i]; i++) {
            if (i == temp_limit) {
                temp_current_page++;
                temp_limit += this.limit;
            }
            row.className = "page-" + temp_current_page;
            console.log(row.className);
        }
        this.total_pages = temp_current_page;
    }
    
    // hides all the articles in the table
    hideAllRows() {
        for (let i = 1; i <= this.total_pages; i++) {
            let cl = document.getElementsByClassName("page-" + i);
            for (let j = 0; j < cl.length; j++) {
                cl[j].style.display = "none";
            }
        }
    }

    displayCurrentPageArticles(page) { 
        this.hideAllRows();
        let to_display_class = document.getElementsByClassName("page-" + page);

        for (let i = 0; i < to_display_class.length; i++) {
            to_display_class[i].style.display = "table-row";
        }
    }

    handleButtons() { 
        if (this.current_page == 1) { 
            prev_button.style.display = "none";
        } else {
            prev_button.style.display = "inline-block";
        }

        if (this.current_page == this.total_pages) {
            next_button.style.display = "none";
        } else {
            next_button.style.display = "inline-block";
        }

        if (this.total_pages == 1) {
            next_button.style.display = "none";
            prev_button.style.display = "none";
        }
    }

    handlePageDisplay() {
        let page_counter = document.getElementById("page-count");
        page_counter.innerHTML = "Page: " + this.current_page + "/" + this.total_pages;
    }

    // main method
    run() {
        // display articles
        let total = document.getElementsByClassName("page-"+this.current_page);
        if (total.length == 0) {
            if (this.current_page != 0) {
                this.current_page--;
            }
        }
        this.displayCurrentPageArticles(this.current_page);

        // managing buttons
        this.handleButtons();

        // display page count
        this.handlePageDisplay();
    }

    // method for "Next" button click
    handleNextButton() {
        this.current_page++;    // increment the page
        this.run();      // display the next articles
    }

    // method for "Previous" button click
    handlePreviousButton() {
        this.current_page--;    // decrement the page
        this.run();      // display the next articles
    }

    toggleDialog(dialog_val, article_val) {
        let dialog = document.getElementById("dialog");
        let article_container = document.getElementById("article-container");

        dialog.hidden = dialog_val;
        article_container.hidden = article_val;
    }

    // method for Dialog
    handleDialog() {
        this.toggleDialog(false, true);
    }

    // method to cancel creating article
    handleCancelButton() {
        this.toggleDialog(true, false);
    }

    async handleDeleteLink(id) {
        let url = "./delete-article/" + id;
        const response = await fetch (url, {
            method: "DELETE",
            headers: {
                'Content-type': 'application/json'
            }
        });
        const data = await response.text();
        return JSON.parse(data);
    }

    helper (button) {
        // remove all the rows from the table
        while (table.rows.length != 0) {
            table.deleteRow(0);
        }
        this.current_page = 1;

        // update DB and get the new list of articles
        article_manager.handleDeleteLink(button.id)
        .then(data => {
            article_manager.addArticleRow(data);
            // add them to the table and classify 
            article_manager.assignClassesToArticles();
            article_manager.run();
        });
    }

    capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    cellGenerator(text, url, cell) {
        // show
        let a = document.createElement("a");
        a.setAttribute("id", text);
        a.setAttribute("href", url)
        text = document.createTextNode(this.capitalizeFirstLetter(text));
        a.appendChild(text);
        cell.appendChild(a);
    }

    addArticleRow(data) {
        let tBody = table.getElementsByTagName('tbody')[0];
        let keys = Object.keys(data);
        console.log(data);
        for (let i = 0; i < keys.length; i++) {
            // if (i == this.limit) {
            //     temp_current_page++;
            //     temp_limit += this.limit;
            // }
            let key = keys[i];
            console.log(i);
            console.log(key);

            let newRow = tBody.insertRow();
            let nameCell = newRow.insertCell();
    
            let showCell = newRow.insertCell();
            let editCell = newRow.insertCell();
            let deleteCell = newRow.insertCell();
    
            nameCell.setAttribute("id", "name");
            let text = document.createTextNode(data[key]['name']);
            nameCell.appendChild(text);
            
            // show
            this.cellGenerator("show", "./article/" + key, showCell);

            // edit
            this.cellGenerator("edit", "./article-edit/" + key, editCell);

            // delete
            a = document.createElement("a");
            a.setAttribute("id", parseInt(key));
            a.setAttribute("class", "delete");
            text = document.createTextNode("Delete")
            a.appendChild(text);
            a.addEventListener("click", function() {
                // remove all the rows from the table
                while (table.rows.length != 0) {
                    table.deleteRow(0);
                }
                this.current_page = 1;

                // update DB and get the new list of articles
                article_manager.handleDeleteLink(a.id)
                .then(data => {
                    article_manager.addArticleRow(data);
                    // add them to the table and classify 
                    article_manager.assignClassesToArticles();
                    article_manager.run();
                });
            })
            deleteCell.appendChild(a);
            
            console.log(newRow);
        }
    }

    
}

let article_manager = new ArticlesManager(10);
article_manager.assignClassesToArticles();

article_manager.run();

window.addEventListener('load', function () {
    article_manager.handleCancelButton();
});

next_button.addEventListener('click', function () {
    article_manager.handleNextButton(); 
});

prev_button.addEventListener('click', function () {
    article_manager.handlePreviousButton();
});

create_article_button.addEventListener('click', function () { 
    article_manager.handleDialog();
});

text_input.addEventListener('keyup', function () { 
    let input = text_input.value;
    if (input.trim() != "") {
        document.getElementById('create').removeAttribute("disabled");
    } else {
        document.getElementById('create').setAttribute("disabled", null);
    }
});

// DELETE 
let delete_buttons = document.getElementsByClassName("delete");

for (let i = 0; i < delete_buttons.length; i++) {
    let button = delete_buttons[i];
    button.addEventListener("click", function () { 
        article_manager.helper(button);
    });
}


