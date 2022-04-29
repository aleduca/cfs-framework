<?php
namespace app\framework\classes;

use Exception;
use ReflectionClass;

class Engine
{
    private ?string $layout;
    private array $data;
    private static string $content;
    private static array $section;
    private static string $actualSection;
    private array $dependencies = [];
    private const TEMPLATE_EXTENSION = 'php';

    /**
     * load method
     *
     * Method used in master template to load the child templates
     *
     * @return string
     */
    private function load():string
    {
        return (self::$content) ?? '';
    }
    
    /**
     * section method
     *
     * Section which I will create to put the data that I get between start and end methods
     *
     * @param string $name
     * @return void
     */
    private function section(string $name)
    {
        echo self::$section[$name] ?? null;
    }


    /**
     * start method
     *
     * This method is used to start the output which I will get in the end method
     *
     * @param string $name name of section
     * @return void
     */
    private function start(string $name)
    {
        ob_start();
        self::$actualSection = $name;
    }
    
    /**
     * end method
     *
     * This method is used to get de output of the data between the start method and this end method
     *
     * @return void
     */
    private function end()
    {
        self::$section[self::$actualSection] = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Method extends
     *
     * @param string $layout Layout a want to use as master template
     * @param array $data data I want to pass to layout(master tenplate)
     * @return void
     */
    private function extends(string $layout, array $data = []):void
    {
        $this->layout = $layout;
        $this->data = $data;
    }

    /**
     * Router Method
     *
     * @param string $name This is the name of the router you want to get
     * @param array $replace (e.g. user/{id}) $this->router('name of router',['id' => 12])
     *
     * @return string
     */
    private function router(string $name, array $replace = [])
    {
        return $this->dependencies['routername']::get($name, $replace);
    }

    /**
     * Class dependencies
     *
     * @param array $dependencies this is an array with all external depencies of the Engine class
     * @return void
     */
    public function dependencies(array $dependencies)
    {
        foreach ($dependencies as $dependency) {
            $className = strtolower((new ReflectionClass($dependency))->getShortName());
            $this->dependencies[$className] = $dependency;
        }
    }

    /**
     * _call magic method
     *
     * This method is only used for the macros methods
     *
     * @param string $name This is the name os the macro method
     * @param array $arguments Arguments to the macro method
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this->dependencies['macros'], $name)) {
            throw new Exception("Macro ${name} does not exist");
        }
        
        if (empty($arguments)) {
            throw new Exception("Macro ${name} need at last one parameter");
        }

        return $this->dependencies['macros']->$name($arguments[0]);
    }

    /**
     * Render method
     *
     * This render method is used on View helper, and the View helper is called in controller
     *
     * @param string $path view to be loaded
     * @param array $data data passed to the view
     * @return void
     */
    public function render(string $path, array $data = [])
    {
        try {
            $view = getViewPath($path, self::TEMPLATE_EXTENSION);

            ob_start();

            extract($data);

            require $view;

            $content = ob_get_contents();

            ob_end_clean();

            if (!empty($this->layout)) {
                self::$content = $content; // content from template (e.g. home or login)
                $data = array_merge($this->data, $data); // this->data came from extends
                return view($this->layout, $data);
            }
            
            return $content;
        } catch (\Throwable $th) {
            echo $th->getMessage(). ' '.$th->getFile() . ' '.$th->getLine();
        }
    }
}
