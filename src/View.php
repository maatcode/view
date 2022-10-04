<?php

namespace Maatcode\View;

use Exception;
use http\Exception\UnexpectedValueException;
use Maatcode\Application\Http\Request;
use Throwable;

class View {

    protected string $layout;
    protected string $content;
    protected array $config;
    protected Request $request;
    protected bool $disableLayout = false;
    protected bool $disableView = false;

    public function render(array $data = [], array $params = []) {
        $content = '';
        $view = $this->request->getAction();
        if (!$this->isDisableView()) {
            try {
                $content = $this->createView($view, $data);
            } catch (Throwable $e) {
                var_dump($e->getMessage());
            }
        }
        else {
            return $data;
        }
        if ($this->isDisableLayout()) {
            echo $content;
            ob_end_flush();
            exit();
        }
        $this->setLayout($this->getConfig()['view']['layout']);
        require_once $this->getLayout();
    }

    public function createView ($view, array $data = [])
    {
        $this->content = '';
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
        $path = $this->getConfig()[strtolower($this->getRequest()->getModule())]['view']['template_path'] ?? null;
        if ($path) {
            try {
                ob_start();
                $includeReturn = require_once $path . $view . '.phtml';
                $this->content = ob_get_clean();
            } catch (Throwable $ex) {
                ob_end_clean();
                throw $ex;
            } catch (Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
            if ($includeReturn === false && empty($this->_content)) {
                throw new UnexpectedValueException(sprintf(
                    '%s: Unable to render template "%s"; file include failed',
                    __METHOD__,
                    $view . '.phtml'
                ));
            }
            return $this->content;
        }
        return null;
    }

    public function getLayout() {
        return $this->layout . '.phtml';
    }

    function setLayout($layout): View
    {
        $this->layout = $layout;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
    public function setConfig($config): View
    {
        $this->config = $config;
        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
    public function setRequest(Request $request): View
    {
        $this->request = $request;
        return $this;
    }

    public function isDisableLayout(): bool
    {
        return $this->disableLayout;
    }
    public function setDisableLayout(bool $disableLayout): View
    {
        $this->disableLayout = $disableLayout;
        return $this;
    }

    public function isDisableView(): bool
    {
        return $this->disableView;
    }
    public function setDisableView(bool $disableView): View
    {
        $this->disableView = $disableView;
        return $this;
    }
    
    

}
