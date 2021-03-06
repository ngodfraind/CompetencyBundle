<?php

namespace HeVinci\CompetencyBundle\Controller;

use HeVinci\CompetencyBundle\Entity\Competency;
use HeVinci\CompetencyBundle\Util\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class CompetencyControllerTest extends UnitTestCase
{
    private $manager;
    private $formHandler;
    private $controller;

    protected function setUp()
    {
        $this->manager = $this->mock('HeVinci\CompetencyBundle\Manager\CompetencyManager');
        $this->formHandler = $this->mock('HeVinci\CompetencyBundle\Form\Handler\FormHandler');
        $this->controller = new CompetencyController($this->manager, $this->formHandler);
    }

    public function testFrameworksAction()
    {
        $this->manager->expects($this->once())
            ->method('listFrameworks')
            ->willReturn('FRAMEWORKS');
        $this->manager->expects($this->once())
            ->method('hasScales')
            ->willReturn(true);
        $this->assertEquals(
            ['frameworks' => 'FRAMEWORKS', 'hasScales' => true],
            $this->controller->frameworksAction()
        );
    }

    public function testNewFrameworkAction()
    {
        $this->manager->expects($this->once())->method('ensureHasScale');
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->with('hevinci_form_framework')
            ->willReturn('FORM');
        $this->assertEquals(['form' => 'FORM'], $this->controller->newFrameworkAction());
    }

    public function testValidCreateFrameworkAction()
    {
        $request = new Request();
        $framework = new Competency();

        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_framework', $request)
            ->willReturn(true);
        $this->formHandler->expects($this->once())
            ->method('getData')
            ->willReturn($framework);
        $this->manager->expects($this->once())
            ->method('persistFramework')
            ->with($framework)
            ->willReturn($framework);

        $this->assertEquals(
            json_encode($framework),
            $this->controller->createFrameworkAction($request)->getContent()
        );
    }

    public function testInvalidCreateFrameworkAction()
    {
        $request = new Request();
        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_framework', $request)
            ->willReturn(false);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->willReturn('FORM');
        $this->assertEquals(['form' => 'FORM'], $this->controller->createFrameworkAction($request));
    }

    public function testFrameworkAction()
    {
        $framework = new Competency();
        $this->manager->expects($this->once())
            ->method('loadFramework')
            ->with($framework)
            ->willReturn('FRAMEWORK');
        $this->assertEquals(
            ['framework' => 'FRAMEWORK'],
            $this->controller->frameworkAction($framework)
        );
    }

    public function testFrameworkEditionFormAction()
    {
        $framework = new Competency();
        $this->manager->expects($this->once())
            ->method('ensureIsRoot')
            ->with($framework);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->with('hevinci_form_framework', $framework)
            ->willReturn('FORM');
        $this->assertEquals(
            ['form' => 'FORM', 'framework' => $framework],
            $this->controller->frameworkEditionFormAction($framework)
        );
    }

    public function testValidEditFrameworkAction()
    {
        $request = new Request();
        $framework = new Competency();

        $this->manager->expects($this->once())
            ->method('ensureIsRoot')
            ->with($framework);
        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_framework', $request, $framework)
            ->willReturn(true);
        $this->manager->expects($this->once())
            ->method('updateCompetency')
            ->with($framework)
            ->willReturn($framework);

        $this->assertEquals(
            json_encode($framework),
            $this->controller->editFrameworkAction($request, $framework)->getContent()
        );
    }

    public function testInvalidEditFrameworkAction()
    {
        $request = new Request();
        $framework = new Competency();

        $this->manager->expects($this->once())
            ->method('ensureIsRoot')
            ->with($framework);
        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_framework', $request, $framework)
            ->willReturn(false);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->willReturn('FORM');
        $this->assertEquals(
            ['form' => 'FORM', 'framework' => $framework],
            $this->controller->editFrameworkAction($request, $framework)
        );
    }

    public function testDeleteCompetencyAction()
    {
        $competency = new Competency();
        $this->manager->expects($this->once())
            ->method('deleteCompetency')
            ->with($competency);
        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\JsonResponse',
            $this->controller->deleteCompetencyAction($competency)
        );
    }

    public function testNewSubCompetencyAction()
    {
        $parent = $this->mock('HeVinci\CompetencyBundle\Entity\Competency');
        $parent->expects($this->once())->method('getId')->willReturn(1);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->with('hevinci_form_competency', null, ['parent_competency' => $parent])
            ->willReturn('FORM');
        $this->assertEquals(
            ['form' => 'FORM', 'parentId' => 1],
            $this->controller->newSubCompetencyAction($parent)
        );
    }

    public function testValidCreateSubCompetencyAction()
    {
        $request = new Request();
        $parent = new Competency();
        $competency = new Competency();

        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_competency', $request, null, ['parent_competency' => $parent])
            ->willReturn(true);
        $this->formHandler->expects($this->once())
            ->method('getData')
            ->willReturn($competency);
        $this->manager->expects($this->once())
            ->method('createSubCompetency')
            ->with($parent, $competency)
            ->willReturn($competency);

        $this->assertEquals(
            json_encode($competency),
            $this->controller->createSubCompetencyAction($request, $parent)->getContent()
        );
    }

    public function testInvalidCreateSubCompetencyAction()
    {
        $request = new Request();
        $parent = $this->mock('HeVinci\CompetencyBundle\Entity\Competency');
        $parent->expects($this->once())->method('getId')->willReturn(1);

        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_competency', $request, null, ['parent_competency' => $parent])
            ->willReturn(false);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->willReturn('FORM');

        $this->assertEquals(
            ['form' => 'FORM', 'parentId' => 1],
            $this->controller->createSubCompetencyAction($request, $parent)
        );
    }

    public function testCompetencyAction()
    {
        $competency = $this->mock('HeVinci\CompetencyBundle\Entity\Competency');
        $competency->expects($this->once())->method('getId')->willReturn(1);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->with('hevinci_form_competency', $competency)
            ->willReturn('FORM');
        $this->assertEquals(
            ['form' => 'FORM', 'id' => 1],
            $this->controller->competencyAction($competency)
        );
    }

    public function testValidEditCompetencyAction()
    {
        $request = new Request();
        $competency = new Competency();

        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_competency', $request, $competency)
            ->willReturn(true);
        $this->manager->expects($this->once())
            ->method('updateCompetency')
            ->with($competency)
            ->willReturn($competency);

        $this->assertEquals(
            json_encode($competency),
            $this->controller->editCompetencyAction($request, $competency)->getContent()
        );
    }

    public function testInvalidEditCompetencyAction()
    {
        $request = new Request();
        $competency = $this->mock('HeVinci\CompetencyBundle\Entity\Competency');
        $competency->expects($this->once())->method('getId')->willReturn(1);

        $this->formHandler->expects($this->once())
            ->method('isValid')
            ->with('hevinci_form_competency', $request, $competency)
            ->willReturn(false);
        $this->formHandler->expects($this->once())
            ->method('getView')
            ->willReturn('FORM');

        $this->assertEquals(
            ['form' => 'FORM', 'id' => 1],
            $this->controller->editCompetencyAction($request, $competency)
        );
    }
}
