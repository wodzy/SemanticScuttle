<?php
require_once 'HTML/QuickForm2/Element/InputText.php';

/**
 * Text input element with pre-set text that vanishes when
 * the user focuses it. Setting a special class is also supported.
 *
 * Example:
 * before:
 *   Name:   [John Do|   ]
 *   E-Mail: [Please type your email address]
 *
 * after:
 *   Name:   [John Doe   ]
 *   E-Mail: [|                             ]
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
 */
class SemanticScuttle_QuickForm2_Element_BackgroundText
    extends HTML_QuickForm2_Element_InputText
{
    /**
     * Background text to use
     *
     * @var string
     */
    protected $btText  = null;

    /**
     * Element class to use when background text is active
     *
     * @var string
     */
    protected $btClass = null;



    /**
     * Sets the background text to show when the text element is
     * empty and not focused
     *
     * @param string $text Background text to set
     *
     * @return SemanticScuttle_QuickForm2_BackgroundText This object
     */
    public function setBackgroundText($text)
    {
        //we add a invisible separator character to distiguish
        // user content from our default text
        $this->btText = $text . "\342\201\243";

        return $this;
    }



    /**
     * Sets the HTML class to use when the text element is
     * empty and not focused
     *
     * @param string $class HTML class to set when the element
     *                      is not focused
     *
     * @return SemanticScuttle_QuickForm2_BackgroundText This object
     */
    public function setBackgroundClass($class)
    {
        $this->btClass = $class;

        return $this;
    }



    /**
     * Updates the attributes array before rendering to prepare
     * for the rendering process.
     *
     * @return void
     */    
    protected function btUpdateAttributes()
    {
        if ($this->btText == '') {
            //deactivate it
            unset($this->attributes['onfocus']);
            unset($this->attributes['onblur']);
            return;
        }

        $jBtText   = json_encode((string)$this->btText);
        $jBtClass  = json_encode($this->btClass);
        $jOldClass = json_encode('');
        if (isset($this->attributes['class'])) {
            $jOldClass = json_encode($this->attributes['class']);
        }

        $this->attributes['onfocus']
            = 'if (this.value == ' . $jBtText . ') {'
            . 'this.value = "";'
            . 'this.className = ' . $jOldClass . ';'
            . '}';
        $this->attributes['onblur']
            = 'if (this.value == "") {'
            . 'this.value = ' . $jBtText . ';'
            . 'this.className = ' . $jBtClass . ';'
            . '}';

        //default when loading the form
        if (!isset($this->attributes['value'])
            || !$this->attributes['value']
        ) {
            $this->attributes['value'] = $this->btText;
        }

        if ($this->attributes['value'] == $this->btText) {
            $this->attributes['class'] = $this->btClass;
        }
    }



    /**
     * Called when the element needs to update its value
     * from form's data sources.
     * This method overwrites the parent one to skip the background text
     * values.
     *
     * @return void
     */
    protected function updateValue()
    {
        $name = $this->getName();
        foreach ($this->getDataSources() as $ds) {
            if (null !== ($value = $ds->getValue($name))
                && $value !== $this->btText
            ) {
                $this->setValue($value);
                return;
            }
        }
    }



    /**
     * Renders the element using the given renderer.
     * Automatically sets the background CSS class if the value
     * is the background text.
     *
     * @param HTML_QuickForm2_Renderer $renderer Renderer instance
     *
     * @return HTML_QuickForm2_Renderer
     */
    public function render(HTML_QuickForm2_Renderer $renderer)
    {
        $this->btUpdateAttributes();

        $renderer->renderElement($this);
        return $renderer;
    }
}
?>