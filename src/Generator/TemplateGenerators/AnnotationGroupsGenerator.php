<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Parser\Elements\Elements;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;

final class AnnotationGroupsGenerator implements TemplateGeneratorInterface
{

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ElementExtractorInterface
     */
    private $elementExtractor;


    public function __construct(
        ConfigurationInterface $configuration,
        TemplateFactory $templateFactory,
        ElementExtractorInterface $elementExtractor
    ) {
        $this->configuration = $configuration;
        $this->templateFactory = $templateFactory;
        $this->elementExtractor = $elementExtractor;
    }


    public function generate(): void
    {
        $annotations = $this->configuration->getOption('annotationGroups');
        foreach ($annotations as $annotation) {
            $template = $this->templateFactory->createNamedForElement(
                TemplateFactory::ELEMENT_ANNOTATION_GROUP,
                $annotation
            );
            $template = $this->setElementsWithAnnotationToTemplate($template, $annotation);
            $template->save();
        }
    }


    private function setElementsWithAnnotationToTemplate(Template $template, string $annotation): Template
    {
        $elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

        $template->setParameters([
            'annotation' => $annotation,
            'hasElements' => (bool) count(array_filter($elements, 'count')),
            'annotationClasses' => $elements[Elements::CLASSES],
            'annotationInterfaces' => $elements[Elements::INTERFACES],
            'annotationTraits' => $elements[Elements::TRAITS],
            'annotationExceptions' => $elements[Elements::EXCEPTIONS],
            'annotationConstants' => $elements[Elements::CONSTANTS],
            'annotationMethods' => $elements[Elements::METHODS],
            'annotationFunctions' => $elements[Elements::FUNCTIONS],
            'annotationProperties' => $elements[Elements::PROPERTIES]
        ]);

        return $template;
    }
}
