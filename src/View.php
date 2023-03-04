<?php
declare(strict_types=1);

namespace Maatcode\View;


use Exception;
use http\Exception\UnexpectedValueException;
use Maatcode\Application\Http\Request;
use Throwable;

class View implements ViewInterface
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
     * @var Request
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
    private array $attributes = [];

    /**
     * @param array $data
     * @param array $params
     * @return array|void
     */
    public function render(array $data = [], array $params = [])
    {
        $content = '';
        $view = $this->request->getAction();
        if (!$this->isDisableView())
        {
            try
            {
                $content = $this->createView($view, $data);
            } catch (Throwable $e)
            {
                var_dump($e->getMessage());
            }
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
     * @return false|string|null
     */
    public function createView($view, array $data = []): false|string|null
    {
        $this->content = '';
        foreach ($data as $key => $value)
        {
            $this->attributes[$key] = $value;
        }
        $path = $this->getConfig()[strtolower($this->getRequest()->getModule())]['view']['template_path'] ?? null;
        if ($path)
        {
            try
            {
                ob_start();
                $includeReturn = require_once $path . $view . '.phtml';
                $this->content = ob_get_clean();
            } catch (Throwable|Exception $ex)
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
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config): View
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
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
     * @return $this
     */
    public function setDisableView(bool $disableView): View
    {
        $this->disableView = $disableView;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }
}
