<?php
namespace app\modules;

use php\sql\SqlException;
use std, gui, framework, app;


class MainModule extends AbstractModule
{
    private $selectedBookId = null;
    private $bookOpen = false;

    function buttonss($book, $button){
    
        $button->on('mouseEnter', function() use ($book, $button) {
             if (!$this->bookOpen) { 
                $this->checkBook($book, $button);
            }
        });
            
        $button->on('click', function() use ($book, $button) {
            $this->bookOpen = true;
            $this->openBook($book, $button, $bookOpen);
        });
            
        $this->buttonExit->on('click', function() use () {
                $this->bookOpen = false;
                $this->closeBook();
        });
            
        $this->button_delete->on('click', function() use ($db) {
            $this->bookOpen = false;
            $this->bookDelete($db);
        });
    }

    function loadGenresAndBooks($selectedGenre = null) {

        $db = $this->database; 
        $db->open();

        $genresResult = $db->query('SELECT * FROM genres');
        
        if ($selectedGenre === null){
            $this->comboboxGenre->items->clear();
            $this->comboboxFind->items->clear();
            $this->comboboxGenre->items->add('Новый жанр');
        }

        foreach ($genresResult as $genreRow) {
        
            $genre = $genreRow->toArray();
            
            if ($selectedGenre === null){
                $this->comboboxGenre->items->add($genre['name']);
                $this->comboboxFind->items->add($genre['name']);
            }
            
            if ($selectedGenre === null || $genre['name'] == $selectedGenre) {
            
                $panel = new UXPanel();
                $panel->size = [520, 20];
                $panel->backgroundColor = '#1a1a1a';
                $panel->borderWidth = '0';
                
                $label = new UXLabel();
                $label->size = [520, 20];
                $label->alignment = 'CENTER';
                $label->text = $genre['name'];
                $label->textColor = "white";
                
                $pane = new UXFlowPane();
                $pane->size = [520, 90];
                $pane->style = '-fx-background-color:#4D3B2F;';
                $pane->vgap = 5;
                
                $pane->add($panel);
                $panel->add($label);
                $this->flowPane->add($pane);
                
                if ($selectedGenre != null){
                    $this->UI(['visible' => true, 'enabled' => true],$this->buttonBack);
                }

                $booksResult = $db->query('SELECT books.*, genres.name as genre_name, books.color FROM books INNER JOIN genres ON books.genre_id = genres.id WHERE genre_id = ?', [$genre['id']]);

                foreach ($booksResult as $bookRow) {
                
                    $book = $bookRow->toArray();
                    
                    $button = new UXFlatButton();
                    $button->size = [20, 60];
                    $button->color = $book['color'];
                    $button->alignment = 'CENTER';
                    $button->cursor = 'HAND';
                    $button->hoverColor = '#b3b3b3';
                    $button->style= '-fx-border-color:#1b1b1b';
                    $pane->add($button);
                    
                    $this->buttonss($book, $button);
                }          
            } 
        } 
        $db->close();
    }
    
    
    function bookDelete($db){
    
       $db = $this->database;
       $db->open();
                        
        if ($this->selectedBookId !== null) {
            $db->query('DELETE FROM books WHERE id = ?', [$this->selectedBookId])->update();
            $this->selectedBookId = null;
            
            $this->UI(['visible' => false, 'enabled' => false],$this->button_delete,$this->panel,$this->buttonExit)
            $this->labelBook->text = "Наведи курсором на книгу (на полке)";
            $this->button->position = ([230,230]);
            
            $this->labelAuthor->text = "Автор неизвестный";
            $this->labelName->text = "Книга удалена";
            
            $this->refreshBooksUI();
        } 
        $db->close(); 
    }
    
    function openBook($book, $button){
    
        $this->selectedBookId = $book['id'];
        $this->textAreaBook->text = $book['description'];
        $this->labelName->text = $book['title'];
        $this->labelAuthor->text = $book['author'];
        $this->labelGenre->text = $book['genre_name'];
        $this->labelBook->text = $book['author'] . " : " . $book['title'];
        $this->panel->borderColor = $button->color;
        
        $this->UI(['visible' => true, 'enabled' => true],$this->buttonExit,$this->panel,$this->button_delete);
        $this->button->position = ([180,230]);
        
        $labelnew = new UXLabel();
        $labelnew->size = [130, 20];
        $labelnew->text = $book['title'];
        $labelnew->textColor = "#000";
        
        $vbcount = $this->vboxHistory->children->count();
        if($vbcount >= 7){$this->vboxHistory->children->removeByIndex(0);}
        $this->vboxHistory->add($labelnew);
    }
    
    function closeBook(){
    
        $this->UI(['visible' => false, 'enabled' => false],$this->button_delete,$this->panel,$this->buttonExit)
        $this->labelBook->text = "Наведи курсором на книгу (на полке)";
        $this->button->position = ([230,230]);
         
    }
    
    function checkBook($book, $button){
    
        if($bookOpen != true){
                        
            $this->labelName->text = $book['title'];
            $this->labelAuthor->text = $book['author'];
            $this->panel3->backgroundColor = $button->color;
            $this->UI(['visible' => false, 'enabled' => false],$this->panel,$this->button_delete);

        }
    }
    
    function UI($properties, ...$elements){
    
        foreach ($elements as $element){
            foreach ($properties as $property => $value) {
                switch ($property) {
                    case 'visible':
                        $element->visible = $value;
                        break;
                    case 'enabled':
                        $element->enabled = $value;
                        break;
                    case 'opacity':
                        $element->opacity = $value;
                        break;    
                }
            }
        }
    }
    
    function searchBooks($searchTerm) {

        $db = $this->database;
        $db->open();
    
        try {
        
            $query = "SELECT books.*, genres.name as genre_name FROM books 
                  INNER JOIN genres ON books.genre_id = genres.id 
                  WHERE books.title LIKE ? OR books.author LIKE ?";
            $searchTerm = '%' . $searchTerm . '%';
            $result = $db->query($query, [$searchTerm, $searchTerm]);
    
            $this->flowPane->children->clear();
    
            foreach ($result as $row) {
            
                $book = $row->toArray();
                
                $button = new UXFlatButton();
                $button->size = [65, 90];
                $button->color = $book['color'];
                $button->text = $book['author'] . "\n" . $book['title'];
                $button->wrapText = true;
                $button->font->size = 10;
                $button->textColor = '#fff';
                $button->alignment = 'CENTER';
                $button->textAlignment = 'CENTER';
                $button->cursor = 'HAND';
                $button->hoverColor = '#b3b3b3';
                $button->style= '-fx-border-color:#1b1b1b';
                $this->flowPane->add($button);
                
                $this->buttonss($book, $button);
                
            }
        } catch (SqlException $e) {
            echo "Database error: " . $e->getMessage();
        }
        
        $this->UI(['visible' => true, 'enabled' => true],$this->buttonBack);
        $db->close();

    }
    
    function addBook(){
        
        $title = $this->editName->text;
        $author = $this->editAuthor->text;
        $desc = $this->textAreaDesc->text;
        $genreName = $this->comboboxGenre->selected;
        
        $db = $this->database;
        $db->open();
        
        if ($genreName == 'Новый жанр') {
        
            $genreName = $this->editGenre->text; 
            $db->query('INSERT INTO genres (name) VALUES (?)', [$genreName])->update();

            $genreResult = $db->query('SELECT id FROM genres WHERE name = ?', [$genreName])->fetch();
            
            if ($genreResult) {
                $genre = $genreResult->toArray();
                $genreId = $genre['id']; 
            } 
        }
        
        else {
            $genreResult = $db->query('SELECT id FROM genres WHERE name = ?', [$genreName])->fetch();
            
            if ($genreResult) {
                $genre = $genreResult->toArray(); 
                $genreId = $genre['id'];
            } 
        }

        $color = $this->colorPicker->value;
        
        $db->query('INSERT INTO books (title, author, description, genre_id, color) VALUES (?, ?, ?, ?, ?)', [$title, $author, $desc, $genreId, $color])->update();
        
        //$this->toast('Книга успешно добавлена');
        
        $this->refreshBooksUI();
        $this->refreshBookAdd();
        $db->close();
        
        $this->rndColor();
        
        $toast = new UXTooltip();
        $toast->text = "Книга успешно добавлена";
        $toast->showByNode($this->panelAdd, 70, 50);
        waitAsync(2000, function () use ($toast) {
            $toast->hide();
        });
    }
    
    function refreshBookAdd(){
        $this->editName->text = '';
        $this->editAuthor->text = '';
        $this->textAreaDesc->text = '';
    }
    
    function refreshBooksUI($selectedGenre = null) {
        $this->flowPane->children->clear();
        $this->loadGenresAndBooks($selectedGenre);
    }

    function rndColor(){
        $randString = str::random(6, 'ABCDFE0123456789');
        $x = "#" . $randString;
        $this->colorPicker->value = UXColor::of($x);
    }


}