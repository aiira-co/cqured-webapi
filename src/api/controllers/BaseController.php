<?php
namespace Api\Controllers;

use Cqured\Router\ActivatedRoute;

/**
 * BaseController Class exists in the Api\Controllers namespace
 * This I use this as a parent Controller to help initialize
 * some value and dependency injection
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class BaseController
{
    protected $page;
    protected $pageSize;
    protected $id;
    protected $category;
    protected $categoryType;
    protected $searchValue;
    protected $searchFields = [];
    protected $uri;
    protected $offset;

    protected $entityId;

    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
        // the ActivatedRoute class contains values of $_GET global
        // Hence, $this->params->foo gets my_security($_GET['foo']).
        // if $_GET['foo'] does not exist, null is returned
        $this->params = new ActivatedRoute;

        $this->page = !is_null($this->params->page) ?
        (int) $this->params->page : 1;

        $this->pageSize = !is_null($this->params->pageSize) ?
        (int) $this->params->pageSize : 10;

        $this->offset = ($this->page - 1) * $this->pageSize;

        if (!is_null($this->params->searchValue)) {
            $this->_searchAlg($this->params->searchValue);
        } else {
            $this->searchValue = '%';
        }

        $this->id = $this->params->id;
        $this->category = $this->params->category;
        $this->categoryType = $this->params->categoryType;

        $this->searchFields = !is_null($this->params->searchFields) ?
        explode(',', $this->params->searchFields) :
        null;

        $this->entityId = 2;
        $this->onInit();
    }

    /**
     * OnInit()
     *
     * @return void
     */
    public function onInit()
    {
    }

    /**
     * Search Key Algorithim
     *
     * @param string $search
     * @return void
     */
    private function _searchAlg(string $search)
    {

        $this->searchValue = '%' . str_replace(' ', '%', $search) . '%';
        // echo $this->searchValue;
    }
    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id
     * to the methodUndocumented function
     *
     * @param integer ...$id
     * @return array|null
     */
    public function httpGet(int...$id): ?array
    {

        return [
            'value1',
            'value2',
        ];
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $form
     * URI: POST: https://api.com/values
     *
     * @param array $form
     * @return array|null
     */
    public function httpPost(array $form): ?array
    {
        $postId = null;
        return [
            'success' => true,
            'alert' => 'We have it at post',
            'id' => $postId,
        ];
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in
     * the uri is passed as int $id to the method
     *
     * @param array $form
     * @param integer $id
     * @return array|null
     */
    public function httpPut(array $form, int $id): ?array
    {
        return [
            'success' => true,
            'alert' => 'We have it at put',
        ];
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in
     * the uri is passed as int ...$id to the method
     *
     * @param integer $id
     * @return array|null
     */
    public function httpDelete(int $id): ?array
    {
        // code here
        return ['id' => $id];
    }
}
