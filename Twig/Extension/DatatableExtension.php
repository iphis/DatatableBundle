<?php

namespace Iphis\DatatableBundle\Twig\Extension;

use Iphis\DatatableBundle\Util\ArrayMerge;
use Iphis\DatatableBundle\Util\Datatable;
use Symfony\Component\Translation\TranslatorInterface;

class DatatableExtension extends \Twig_Extension
{
    use ArrayMerge;

    protected $callbackMethodName = array(
        "createdRow",
        "drawCallback",
        "footerCallback",
        "formatNumber",
        "headerCallback",
        "infoCallback",
        "initComplete",
        "preDrawCallback",
        "rowCallback",
        "stateLoadCallback",
        "stateLoaded",
        "stateLoadParams",
        "stateSaveCallback",
        "stateSaveParams",
    );

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'datatable', [$this, 'datatable'],
                ["is_safe" => ["html"], 'needs_environment' => true]
            ),
            new \Twig_SimpleFunction(
                'datatable_html', [$this, 'datatableHtml'],
                ["is_safe" => ["html"], 'needs_environment' => true]
            ),
            new \Twig_SimpleFunction(
                'datatable_js', [$this, 'datatableJs'],
                ["is_safe" => ["html"], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'printDatatableOption', [$this, 'printDatatableOption'],
                ["is_safe" => ["html"]]
            ),
        ];
    }

    /**
     * Converts a string to time
     *
     * @param \Twig_Environment $twig
     * @param                   $options
     * @return string
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function datatable(\Twig_Environment $twig, $options)
    {
        $options = $this->buildDatatableTemplate($options);

        $mainTemplate = array_key_exists('main_template', $options) ? $options['main_template'] : 'IphisDatatableBundle:Main:index.html.twig';

        return $twig->render($mainTemplate, $options);
    }

    /**
     * Converts a string to time
     *
     * @param \Twig_Environment $twig
     * @param                   $options
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function datatableJs(\Twig_Environment $twig, $options)
    {
        $options = $this->buildDatatableTemplate($options, "js");

        $mainTemplate = array_key_exists('main_template', $options) ? $options['js_template'] : 'IphisDatatableBundle:Main:datatableJs.html.twig';

        return $twig->render($mainTemplate, $options);
    }

    /**
     * Converts a string to time
     *
     * @param \Twig_Environment $twig
     * @param                   $options
     * @return string
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function datatableHtml(\Twig_Environment $twig, $options)
    {
        if (!isset($options['id'])) {
            $options['id'] = 'ali-dta_'.md5(mt_rand(1, 100));
        }
        $dt = Datatable::getInstance($options['id']);

        $options['fields'] = $dt->getFields();
        $options['search'] = $dt->getSearch();
        $options['searchFields'] = $dt->getSearchFields();
        $options['multiple'] = $dt->getMultiple();

        $mainTemplate = 'IphisDatatableBundle:Main:datatableHtml.html.twig';

        if (isset($options['html_template'])) {
            $mainTemplate = $options['html_template'];
        }

        return $twig->render($mainTemplate, $options);
    }

    /**
     * @param      $options
     * @param null $type
     * @return mixed
     * @throws \Exception
     */
    private function buildDatatableTemplate($options, $type = null)
    {
        if (!isset($options['id'])) {
            $options['id'] = 'ali-dta_'.md5(mt_rand(1, 100));
        }

        $dt = Datatable::getInstance($options['id']);

        $config = $dt->getConfiguration();

        $options['js'] = array_merge($config['js'], $options['js']);
        $options['fields'] = $dt->getFields();
        $options['search'] = $dt->getSearch();
        $options['global_search'] = $dt->getGlobalSearch();
        $options['multiple'] = $dt->getMultiple();
        $options['searchFields'] = $dt->getSearchFields();
        $options['sort'] = $dt->getOrderField() === null ? null : array(
            array_search($dt->getOrderField(), array_values($dt->getFields())),
            $dt->getOrderType(),
        );

        if ($type == "js") {
            $this->buildJs($options, $dt);
        }

        return $options;
    }

    /**
     * @param           $options
     * @param Datatable $dt
     */
    private function buildJs(&$options, $dt)
    {
        if (array_key_exists("ajax", $options['js']) && !is_array($options['js']['ajax'])) {
            $options['js']['ajax'] = array(
                "url" => $options['js']['ajax'],
                "type" => "POST",
            );
        }

        if (count($dt->getHiddenFields()) > 0) {
            $options['js']['columnDefs'][] = array(
                "visible" => false,
                "targets" => $dt->getHiddenFields(),
            );
        }
        if (count($dt->getNotSortableFields()) > 0) {
            $options['js']['columnDefs'][] = array(
                "orderable" => false,
                "targets" => $dt->getNotSortableFields(),
            );
        }
        if (count($dt->getNotFilterableFields()) > 0) {
            $options['js']['columnDefs'][] = array(
                "searchable" => false,
                "targets" => $dt->getNotFilterableFields(),
            );
        }

        $this->buildTranslation($options);
    }

    /**
     * @param $options
     */
    private function buildTranslation(&$options)
    {
        if (!array_key_exists("language", $options['js'])) {
            $options['js']['language'] = array();
        }

        $baseLanguage = array(
            "processing" => $this->translator->trans("datatable.datatable.processing"),
            "search" => $this->translator->trans("datatable.datatable.search"),
            "lengthMenu" => $this->translator->trans("datatable.datatable.lengthMenu"),
            "info" => $this->translator->trans("datatable.datatable.info"),
            "infoEmpty" => $this->translator->trans("datatable.datatable.infoEmpty"),
            "infoFiltered" => $this->translator->trans("datatable.datatable.infoFiltered"),
            "infoPostFix" => $this->translator->trans("datatable.datatable.infoPostFix"),
            "loadingRecords" => $this->translator->trans("datatable.datatable.loadingRecords"),
            "zeroRecords" => $this->translator->trans("datatable.datatable.zeroRecords"),
            "emptyTable" => $this->translator->trans("datatable.datatable.emptyTable"),
            "searchPlaceholder" => $this->translator->trans("datatable.datatable.searchPlaceholder"),
            "paginate" => array(
                "first" => $this->translator->trans("datatable.datatable.paginate.first"),
                "previous" => $this->translator->trans("datatable.datatable.paginate.previous"),
                "next" => $this->translator->trans("datatable.datatable.paginate.next"),
                "last" => $this->translator->trans("datatable.datatable.paginate.last"),
            ),
            "aria" => array(
                "sortAscending" => $this->translator->trans("datatable.datatable.aria.sortAscending"),
                "sortDescending" => $this->translator->trans("datatable.datatable.aria.sortDescending"),
            ),
        );

        $options['js']['language'] = $this->arrayMergeRecursiveDistinct($baseLanguage, $options['js']['language']);
    }

    /**
     * @param $var
     * @param $elementName
     * @return string
     */
    public function printDatatableOption($var, $elementName)
    {
        if (is_bool($var)) {
            return $var === true ? 'true' : 'false';
        }

        if (is_array($var)) {
            return json_encode($var);
        }

        if (in_array($elementName, $this->callbackMethodName)) {
            return $var;
        }

        return json_encode($var);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'DatatableBundle';
    }
}
