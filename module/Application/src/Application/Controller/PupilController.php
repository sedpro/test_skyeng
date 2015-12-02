<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class PupilController extends AbstractActionController
{
    /**
     * Create a new pupil
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction()
    {
        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()->get('PupilForm');

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $values = $form->getData();

                $this->getPupilService()->insert($values);

                $this->flashMessenger()->addSuccessMessage('New pupil successfully added.');

                return $this->redirect()->toRoute('teachers');
            }
        }

        return new ViewModel(['form' => $form]);

    }

    /**
     * Get autoload suggestions
     *
     * @return array|JsonModel
     */
    public function ajaxAutocompleteAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->notFoundAction();
        }

        $maxSuggestions = 10;
        $suggestions = [];

        $teacher_id = $this->params()->fromRoute('teacher_id');

        $pupilService = $this->getPupilService();
        $currentPupils = $pupilService->getByTeacherIds($teacher_id);
        $currentPupilIds = [];
        foreach ($currentPupils as $pupil) {
            $currentPupilIds[] = $pupil->id;
        }

        $query = $this->params()->fromQuery('query');
        if ($query) {
            $pupils = $pupilService->getByFirstLettersOfName($query, $maxSuggestions, $currentPupilIds);

            foreach ($pupils as $pupil) {
                $suggestions[] = [
                    'value' => $pupil->name,
                    'pupil_id' => $pupil->id,
                ];
            }
        }

        return new JsonModel([
            'query' => $query,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * @return \Application\Service\Pupil
     */
    private function getPupilService()
    {
        return $this->getServiceLocator()->get('PupilService');
    }
}
