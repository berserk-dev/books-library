<?php
namespace app\forms;

use php\sql\SqlException;
use std, gui, framework, app;


class MainForm extends AbstractForm
{

    /**
     * @event construct 
     */
    function doConstruct(UXEvent $e = null)
    {    
        $this->loadGenresAndBooks();
        $this->rndColor();
        $this->labelColor->opacity = 1;
    }

    /**
     * @event buttonAddEx.click-Left 
     */
    function doButtonAddExClickLeft(UXMouseEvent $e = null)
    {    
        $this->UI(['opacity' => 1],$this->flowPane, $this->panelAlt);
        $this->UI(['enabled' => true],$this->flowPane, $this->panelAlt);
        $this->UI(['visible' => false, 'enabled' => false],$this->panelAdd, $this->labelColor, $this->colorPicker);
    }

    /**
     * @event comboboxGenre.action 
     */
    function doComboboxGenreAction(UXEvent $e = null)
    {    
        $x = $this->comboboxGenre->selected;
        if($x == 'Новый жанр'){
            $this->UI(['visible' => true, 'enabled' => true],$this->buttonGEx, $this->editGenre);
            $this->UI(['visible' => false, 'enabled' => false],$this->comboboxGenre);
        } 
    }

    /**
     * @event buttonGEx.click-Left 
     */
    function doButtonGExClickLeft(UXMouseEvent $e = null)
    {    
        $this->UI(['visible' => false, 'enabled' => false],$this->buttonGEx, $this->editGenre);
        $this->UI(['visible' => true, 'enabled' => true],$this->comboboxGenre);
    }

    /**
     * @event button5.click-Left 
     */
    function doButton5ClickLeft(UXMouseEvent $e = null)
    {    
        $this->addBook();
    }

    /**
     * @event buttonBack.click-Left 
     */
    function doButtonBackClickLeft(UXMouseEvent $e = null)
    {    
        $this->buttonBack->visible = false;
        $this->buttonBack->enabled = false;
        $this->refreshBooksUI();
    }

    /**
     * @event button3.click-Left 
     */
    function doButton3ClickLeft(UXMouseEvent $e = null)
    {
        app()->hideForm($this->getContextFormName());
    }

    /**
     * @event buttonFind.click-Left 
     */
    function doButtonFindClickLeft(UXMouseEvent $e = null)
    {
        $selectedGenre = $this->comboboxFind->selected;
        $this->refreshBooksUI($selectedGenre);
    }

    /**
     * @event buttonAlt.click-Left 
     */
    function doButtonAltClickLeft(UXMouseEvent $e = null)
    {
        $searchTerm = $this->edit->text;
        $this->searchBooks($searchTerm);
        
    }

    /**
     * @event comboboxFind.action 
     */
    function doComboboxFindAction(UXEvent $e = null)
    {
        $this->UI(['visible' => true, 'enabled' => true],$this->buttonFind);
    }

    /**
     * @event button.click-Left 
     */
    function doButtonClickLeft(UXMouseEvent $e = null)
    {
        $this->labelColor->visible = true;
        $this->UI(['opacity' => 0.5],$this->flowPane, $this->panelAlt);
        $this->UI(['enabled' => false],$this->flowPane, $this->panelAlt)
        $this->UI(['visible' => true, 'enabled' => true],$this->panelAdd, $this->colorPicker);
    }

    /**
     * @event button6.click-Left 
     */
    function doButton6ClickLeft(UXMouseEvent $e = null)
    {    
        app()->minimizeForm($this->getContextFormName());
    }



    


    


    

    







    





   



}
