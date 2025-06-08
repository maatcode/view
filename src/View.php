<?php
declare(strict_types=1);

namespace Maatcode\View;

use http\Exception\UnexpectedValueException;
use Maatcode\Application\Http\Request;
use Throwable;

/**
 *
 */
class View
{

    /**
     * @var string
     */
    protected string $layout;
    /**
     * @var string
     */
    protected string $content;
    /**
     * @var array
     */
    protected array $config;
    /**
     * @var \Maatcode\Application\Http\Request
     */
    protected Request $request;
    /**
     * @var bool
     */
    protected bool $disableLayout = false;
    /**
     * @var bool
     */
    protected bool $disableView = false;
    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * @param array $data
     *
     * @return array|void
     * @throws \Throwable
     */
    public function render(array $data = [])
    {
        $content = '';
        $view = $this->request->getAction();
        if (!$this->isDisableView())
        {
            $content = $this->createView($view, $data);
        }
        else
        {
            return $data;
        }
        if ($this->isDisableLayout())
        {
            echo $content;
            ob_end_flush();
            exit();
        }
        $this->setLayout($this->getConfig()['view']['layout']);
        require_once $this->getLayout();
    }

    /**
     * @param $view
     * @param array $data
     *
     * @return false|string|null
     */
    public function createView($view, array $data = []): false|string|null
    {
        $this->content = '';
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
        $path = $this->getConfig()[strtolower($this->getRequest()->getModule())]['view']['template_path'] ?? null;
        if ($path)
        {
            try
            {
                ob_start();
                $includeReturn = require_once $path . $view . '.phtml';
                $this->content = ob_get_clean();
            } catch (Throwable $ex)
            {
                ob_end_clean();
                throw $ex;
            }
            if ($includeReturn === false && empty($this->_content))
            {
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

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout . '.phtml';
    }

    /**
     * @param $layout
     *
     * @return $this
     */
    public function setLayout($layout): View
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $config
     *
     * @return $this
     */
    public function setConfig($config): View
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return \Maatcode\Application\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param \Maatcode\Application\Http\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request): View
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableLayout(): bool
    {
        return $this->disableLayout;
    }

    /**
     * @param bool $disableLayout
     *
     * @return $this
     */
    public function setDisableLayout(bool $disableLayout): View
    {
        $this->disableLayout = $disableLayout;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableView(): bool
    {
        return $this->disableView;
    }

    /**
     * @param bool $disableView
     *
     * @return $this
     */
    public function setDisableView(bool $disableView): View
    {
        $this->disableView = $disableView;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     */
    public function __set($key, $value): void
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function __get($key): string
    {
        return $this->properties[$key] ?? '';
    }


}
