<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class TeacherController extends AbstractActionController
{
    /**
     * Show list of all teachers
     *
     * @return array|ViewModel
     */
    public function indexAction()
    {

        $page = (int)$this->params()->fromRoute('page', 1);
        $itemsPerPage = (int)$this->params()->fromRoute('items_per_page');

        $results = $this->getTeacherService()->getItemsForPaginator($page, $itemsPerPage);
        $items = $results['items'];
        $count = $results['count'];
        $pupils = $results['pupils'];

        $paginator = $this->getPaginator($count, $page, $itemsPerPage);

        if ($page > $paginator->getCurrentPageNumber()) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'paginator' => $paginator,
            'items'     => $items,
            'pupils'    => $pupils,
        ]);
    }

    /**
     * Show list of all teachers, who have only pupils, born in a certain month
     *
     * @return array|ViewModel
     */
    public function monthAction()
    {
        $month = (int)$this->params()->fromRoute('month');
        $page = (int)$this->params()->fromRoute('page', 1);
        $itemsPerPage = (int)$this->params()->fromRoute('items_per_page', 10);

        $results = $this->getTeacherService()->getTeachersOfPupilsBornInMonth($month, $page, $itemsPerPage);
        $items = $results['items'];
        $count = $results['count'];

        $paginator = $this->getPaginator($count, $page, $itemsPerPage);

        if ($page > $paginator->getCurrentPageNumber()) {
            return $this->notFoundAction();
        }

        return new ViewModel([
            'paginator' => $paginator,
            'items'     => $items,
            'monthName' => strtolower(date("F", mktime(0, 0, 0, $month, 1, 2011))),
            'month'     => $month,
        ]);
    }

    /**
     * Create a new teacher
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()->get('TeacherForm');

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $values = $form->getData();

                $this->getTeacherService()->insert($values);

                $this->flashMessenger()->addSuccessMessage('New teacher successfully added.');

                return $this->redirect()->toRoute('teachers');
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Assign pupils to a teacher
     *
     * @return array|ViewModel
     */
    public function assignAction()
    {
        $id = (int)$this->params()->fromRoute('id');

        $teacher = $this->getTeacherService()->getItem(['id' => $id]);

        if (!$teacher) {
            return $this->notFoundAction();
        }

        $pupils = $this->getPupilService()->getByTeacherIds($id);

        return new ViewModel([
            'teacher' => $teacher,
            'pupils'  => $pupils,
        ]);
    }

    /**
     * Show two teachers, who have largest amount of common pupils, and these common pupils too
     *
     * @return ViewModel
     */
    public function maxAction()
    {
        $teacherIds = $this->getTeacherPupilService()->getTeachersWithMostCommonPupils();
        $teachers = $this->getTeacherService()->getItemsWhere(['id' => [$teacherIds->first, $teacherIds->second]]);
        $pupils = $this->getPupilService()->getCommonPupilsByTeacherIds($teacherIds->first, $teacherIds->second);

        return new ViewModel([
            'teachers' => $teachers,
            'pupils'   => $pupils,
        ]);
    }

    /**
     * Create a link between a teacher and a pupil
     *
     * @return array|JsonModel
     */
    public function ajaxLinkAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->notFoundAction();
        }

        $pupil_id = (int)$this->params()->fromPost('pupil_id');
        if (!$pupil_id) {
            return new JsonModel(['error' => 'no pupil_id']);
        }

        $teacher_id = (int)$this->params()->fromPost('teacher_id');
        if (!$teacher_id) {
            return new JsonModel(['error' => 'no teacher_id']);
        }

        $teacherPupilService = $this->getTeacherPupilService();
        $link = $teacherPupilService->getItem([
            'teacher_id' => $teacher_id,
            'pupil_id' => $pupil_id,
        ]);

        if (!$link) {
            $teacherPupilService->insert([
                'teacher_id' => $teacher_id,
                'pupil_id' => $pupil_id,
            ]);
        }

        $pupil = $this->getPupilService()->getItem(['id' => $pupil_id]);

        $html = $this->getPartialView('application/teacher/pupil_item', ['pupil' => $pupil]);

        return new JsonModel([
            'success' => true,
            'html'    => $html,
        ]);
    }

    /**
     * Delete a link between a teacher and a pupil
     *
     * @return array|JsonModel
     */
    public function ajaxUnlinkAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->notFoundAction();
        }

        $pupil_id = (int)$this->params()->fromPost('pupil_id');
        if (!$pupil_id) {
            return new JsonModel(['error' => 'no pupil_id']);
        }

        $teacher_id = (int)$this->params()->fromPost('teacher_id');
        if (!$teacher_id) {
            return new JsonModel(['error' => 'no teacher_id']);
        }

        $teacherPupilService = $this->getTeacherPupilService();
        $link = $teacherPupilService->getItem([
            'teacher_id' => $teacher_id,
            'pupil_id' => $pupil_id,
        ]);

        if ($link) {
            $teacherPupilService->delete($link->id);
        }

        return new JsonModel([
            'success' => true,
        ]);
    }

    /**
     * @param $count
     * @param $page
     * @param $itemsPerPage
     * @return \Zend\Paginator\Paginator
     */
    private function getPaginator($count, $page, $itemsPerPage)
    {
        $adapter = new \Zend\Paginator\Adapter\NullFill($count);

        $paginator = new \Zend\Paginator\Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($itemsPerPage);
        $paginator->setPageRange(10);

        return $paginator;
    }

    /**
     * @return \Application\Service\Teacher
     */
    private function getTeacherService()
    {
        return $this->getServiceLocator()->get('TeacherService');
    }

    /**
     * @return \Application\Service\Pupil
     */
    private function getPupilService()
    {
        return $this->getServiceLocator()->get('PupilService');
    }

    /**
     * @return \Application\Service\TeacherPupil
     */
    private function getTeacherPupilService()
    {
        return $this->getServiceLocator()->get('TeacherPupilService');
    }

    /**
     * Get html from template
     *
     * @param $template
     * @param $data
     * @return string
     */
    private function getPartialView($template, $data = [])
    {
        $viewModel = new ViewModel($data);
        $viewModel->setTemplate($template);
        $renderer = $this->getServiceLocator()->get('ViewManager')->getRenderer();
        $html = $renderer->render($viewModel);

        return $html;
    }
}
