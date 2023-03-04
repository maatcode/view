<?php
declare(strict_types=1);

namespace Maatcode\View;

use Maatcode\Application\Http\Request;

interface ViewInterface
{
    /**
     * @param array $data
     * @param array $params
     * @return array|void
     */
    public function render(array $data = [], array $params = []);

    /**
     * @param $view
     * @param array $data
     * @return false|string|null
     */
    public function createView($view, array $data = []): false|string|null;

    /**
     * @return string
     */
    public function getLayout(): string;

    /**
     * @param $layout
     * @return $this
     */
    public function setLayout($layout): View;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config): View;

    /**
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * @param Request $request
     * @return View
     */
    public function setRequest(Request $request): View;

    /**
     * @return bool
     */
    public function isDisableLayout(): bool;

    /**
     * @param bool $disableLayout
     * @return View
     */
    public function setDisableLayout(bool $disableLayout): View;

    /**
     * @return bool
     */
    public function isDisableView(): bool;

    /**
     * @param bool $disableView
     * @return View
     */
    public function setDisableView(bool $disableView): View;
}
