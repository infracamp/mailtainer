<?php


namespace App\Cms;


use Leuffen\TextTemplate\TextTemplate;
use Phore\FileSystem\PhoreFile;
use Phore\Html\Fhtml\FHtml;
use Phore\MicroApp\Response\HtmlResponse;
use Phore\MicroApp\Type\Mime;
use Phore\MicroApp\Type\Request;
use Phore\VCS\VcsRepository;

class CmsCtrl
{

    const ROUTE = "::path";


    protected $host = false;

    /**
     * @var \Phore\FileSystem\PhoreDirectory
     */
    protected $path;

    public function __construct(Request $request, VcsRepository $vcsRepository)
    {
        $hosts = $vcsRepository->object(CONFIG_REPO_PREFIX . "/httpcfg.yml")->getYaml();

        foreach ($hosts as $curHost) {
            $host = phore_pluck("host", $curHost, null);
            $aliases = phore_pluck("aliases", $curHost, []);
            $path = phore_pluck("path", $curHost, new \InvalidArgumentException("httpcfg.yml: Host $host is missing path:-configuration."));

            if (strcasecmp($request->httpHost, $host)) {
                $this->host = $host;
                $this->path = phore_dir("/" . REPO_PATH . "/" . CONFIG_REPO_PREFIX)->withSubPath($path)->assertDirectory();
            }
        }

    }



    protected function _splitMarkdown(string $input)
    {
        $input = explode("\n", $input);
        $yaml = "";
        if (count($input) > 0 && $input[0] == "---") {
            $input[0] = "";

            for ($i=1; $i<count($input); $i++) {
                if ($input[$i] == "---") {
                    $input[$i] = "";
                    break;
                }
                $yaml .= "\n" . $input[$i];
                $input[$i] = "";
            }
        }
        return [implode("\n", $input), yaml_parse($yaml)];
    }


    public function renderInTemplate(string $template, array $scope)
    {
        $tpl = $this->path->withFileName($template)->assertFile()->get_contents();
        echo $tpl;
        $template = new TextTemplate($tpl );
        return $template->apply($scope, false);
    }


    public function renderFile (PhoreFile $file)
    {
        if ($file->getExtension() === "md") {
            [$mdStr, $data] = $this->_splitMarkdown($file->get_contents());

            $template = phore_pluck("tpl", $data, "default.tpl.html");
            $data["content"] = Fhtml::Markdown($mdStr)->render();

            return $this->renderInTemplate($template, $data);

        }
        throw new \InvalidArgumentException("cannot parse $file");
    }


    public function on_get(string $path, Request $request)
    {
        if ($this->host === false) {
            throw new \InvalidArgumentException("httpcfg.yml: No config for host '{$request->httpHost}'");
        }
        $curPath = $this->path->withSubPath($path);
        if ($curPath->isFile()) {
            $mime = new Mime();
            $contentType = $mime->getContentTypeByExtension($curPath->getExtension());
            header("Content-Type: $contentType; charset=utf-8");
            echo $curPath->assertFile()->get_contents();
            return true;
        }

        if ($curPath->isDirectory()) {
            $index = $curPath->withFileName("index.md");
            if ($index->isFile()) {
                return new HtmlResponse($this->renderFile($index));
            }
        }

        return true;
    }

}
