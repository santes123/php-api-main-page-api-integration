<?php

namespace App\App;

class Router
{
    private array $routes = [];
    private array $global = [];

    public function use(object $mw): void
    {
        $this->global[] = $mw;
    }
    private function add(string $m, string $p, $h, array $mw = []): void
    {
        $this->routes[] = compact('m', 'p', 'h', 'mw');
    }
    public function get($p, $h, $mw = [])
    {
        $this->add('GET', $p, $h, $mw);
    }
    public function post($p, $h, $mw = [])
    {
        $this->add('POST', $p, $h, $mw);
    }
    public function put($p, $h, $mw = [])
    {
        $this->add('PUT', $p, $h, $mw);
    }
    public function delete($p, $h, $mw = [])
    {
        $this->add('DELETE', $p, $h, $mw);
    }

    public function dispatch(Request $req, Response $res): void
    {
        foreach ($this->routes as $r) {
            if ($r['m'] !== $req->method) continue;
            $pat = '#^' . preg_replace('#:([a-zA-Z_][a-zA-Z0-9_]*)#', '(?P<$1>[^/]+)', $r['p']) . '$#';
            if (preg_match($pat, $req->uri, $m)) {
                $req->params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $stack = array_merge($this->global, $r['mw']);
                $this->run($stack, $req, $res, $r['h']);
                return;
            }
        }
        $res->json(['error' => 'Not Found'], 404);
    }

    private function run(array $stack, Request $req, Response $res, $handler, int $i = 0): void
    {
        if ($i < count($stack)) {
            $mw = $stack[$i];
            $mw->handle($req, $res, fn() => $this->run($stack, $req, $res, $handler, $i + 1));
        } else {
            if (is_array($handler)) {
                [$obj, $m] = $handler;
                $obj->$m($req, $res);
            } else {
                $handler($req, $res);
            }
        }
    }
}
