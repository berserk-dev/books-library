<?php
namespace app\forms;

use php\sql\SqlException;
use std, gui, framework, app;


class addBook extends AbstractForm
{

    /**
     * @event buttonAlt.click-Left 
     */
    function doButtonAltClickLeft(UXMouseEvent $e = null)
    {
        app()->hideForm($this->getContextFormName());
        $this->button->position = ([190,700]);
    }

    /**
     * @event colorPicker44.click-Left 
     */
    function doColorPicker44ClickLeft(UXMouseEvent $e = null)
    {    
        $this->rect->fillColor = $this->colorPicker->value;
    }

    /**
     * @event colorPicker44.action 
     */
    function doColorPicker44Action(UXEvent $e = null)
    {    
        $this->rect->fillColor = $this->colorPicker->value;
    }

    /**
     * @event labelAlt.construct 
     */
    function doLabelAltConstruct(UXEvent $e = null)
    {    
        $this->labelAlt->opacity = 1;
    }

    /**
     * @event comboboxGenre.action 
     */
    function doComboboxGenreAction(UXEvent $e = null)
    {    
        $x = $this->comboboxGenre->selected;
        
        if($x == 'Новый жанр'){
            $this->editGenre->enabled = true;
            $this->editGenre->visible = true;
            $this->button3->enabled = true;
            $this->button3->visible = true;
            $this->comboboxGenre->enabled = false;
            $this->comboboxGenre->visible = false;
        }
    }

    /**
     * @event button3.click-Left 
     */
    function doButton3ClickLeft(UXMouseEvent $e = null)
    {    
        $this->editGenre->enabled = false;
        $this->editGenre->visible = false;
        $this->button3->enabled = false;
        $this->button3->visible = false;
        $this->comboboxGenre->enabled = true;
        $this->comboboxGenre->visible = true; 
    }

    /**
     * @event construct 
     */
    function doConstruct(UXEvent $e = null)
    {    
        $this->loadGenres();
    }

    /**
     * @event button.click-Left 
     */
    function doButtonClickLeft(UXMouseEvent $e = null)
    {    
    
        $title = $this->editName->text;
        $author = $this->editAuthor->text;
        $desc = $this->textAreaDesc->text;
        $genreName = $this->comboboxGenre->selected;
    
      

    try {
        $db = $this->database;

        // Проверка, выбран ли "Новый жанр"
        if ($genreName == 'Новый жанр') {
            $genreName = $this->editGenre->text; // Используем введенный жанр
            // Добавляем новый жанр в базу данных
            $db->query('INSERT INTO genres (name) VALUES (?)', [$genreName])->update();
        
            // Выполняем запрос на получение id только что добавленного жанра
            $genreResult = $db->query('SELECT id FROM genres WHERE name = ?', [$genreName])->fetch();
            if ($genreResult) {
                $genre = $genreResult->toArray(); // Преобразуем результат в массив
                $genreId = $genre['id']; // Получаем id жанра
            } else {
                // Если по каким-то причинам жанр не найден после вставки, выводим ошибку
                $this->showErrorMessage("Вставленный жанр не найден.");
                return; // Прерываем выполнение функции
            }
        
        }
         else {
            // Получаем id жанра
            $genreResult = $db->query('SELECT id FROM genres WHERE name = ?', [$genreName])->fetch();
            
            if ($genreResult) {
                $genre = $genreResult->toArray(); // Преобразуем результат в массив
                $genreId = $genre['id']; // Получаем id жанра
            } else {
                alert("Жанр '$genreName' не найден.");
                return; // Прерываем выполнение функции, если жанр не найден
            }
        }

        // Добавляем книгу в базу данных
        $db->query('INSERT INTO books (title, author, description, genre_id) VALUES (?, ?, ?, ?)', [$title, $author, $desc, $genreId])->update();
        // Обновляем интерфейс
        
        
        $this->toast('Книга успешно добавлена');
        app()->form('MainForm')->refreshBooksUI();
        #app()->hideForm($this->getContextFormName());
        
        #$this->refreshBooksUI();
        } catch (SqlException $e) {
            $this->showErrorMessage("Ошибка при добавлении книги: " . $e->getMessage());
        }
        
         
    }


   

    
    private function loadGenres() {
    
        $db = $this->database;
        try {
            $genresResult = $db->query('SELECT * FROM genres');
    
            foreach ($genresResult as $genreRow) {
                $genre = $genreRow->toArray();
                $this->comboboxGenre->items->add($genre['name']);
            }
            $this->comboboxGenre->items->add('Новый жанр');
            
        } catch (SqlException $e) {
            $this->showErrorMessage("Ошибка при загрузке жанров: " . $e->getMessage());
        }
}


}
