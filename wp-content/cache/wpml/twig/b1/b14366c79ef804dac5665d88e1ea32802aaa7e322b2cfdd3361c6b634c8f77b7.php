<?php

/* /setup/store-pages.twig */
class __TwigTemplate_dc3e982f510f6be14a237ef4d83a54cd8542ef25a86a1270a2a4fcd55c7ef7b2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<h1>";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "heading", array()), "html", null, true);
        echo "</h1>

<p>";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "description", array()), "html", null, true);
        echo "</p>

";
        // line 5
        echo ($context["store_pages"] ?? null);
        echo "

<p class=\"wcml-setup-actions step\">
    <a href=\"";
        // line 8
        echo twig_escape_filter($this->env, ($context["continue_url"] ?? null), "html", null, true);
        echo "\" class=\"button button-primary button-large submit\">";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "continue", array()), "html", null, true);
        echo "</a>
</p>
";
    }

    public function getTemplateName()
    {
        return "/setup/store-pages.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 8,  30 => 5,  25 => 3,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "/setup/store-pages.twig", "/var/www/html/wingparent/wp-content/plugins/woocommerce-multilingual/templates/setup/store-pages.twig");
    }
}
