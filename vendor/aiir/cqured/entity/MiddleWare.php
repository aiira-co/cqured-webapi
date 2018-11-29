<?php
namespace Cqured\Entity;

/**
 * MiddleWare Class exists in the Cqured\Entity namespace
 * This class is simply a filter between the component and the model
 * They are effective in POST queries to strip tags and etc
 *
 * @category Entity
 */

class MiddleWare
{
    /**
     * Compares the $_POST and Original Data
     * for changes.
     * Returns empty array is no changes were made
     *
     * @param [type] $default
     * @param array $post
     * @return array
     */
    public function filterPost($default, array $post = null):array
    {
        // Set $post to default if its null
        $post = $post??$_POST;


        // store array keys or members for comparison
        $default = json_decode(json_encode($default), true);
        // print_r($default);

        $postKeys = array_keys($post);
        $defaultKeys = array_keys($default);

        // echo '<br/> post is';
        // print_r($postKeys);
        // echo '<br/> default is:';
        // print_r($defaultKeys);

        //count the numbers if members for the parameters

        $postCount = count($post);
        $defaultCount = count($default);

        // $update = [];

        for ($i =0; $i < $postCount; $i++) {
            // First check if the member exists,
            // if yes, compare values,
            //   if same, ignoire,
            //   if not the same value, add to a custom array,
            // if no, ignore

            // $update = array();

            if (in_array($postKeys[$i], $defaultKeys)) {
                // echo $postKeys[$i].' --> '.$post[$postKeys[$i]].'<br/>';
                $key =$postKeys[$i];
                $value = $post[$key];
                if ($post[$key] != $default[$key]) {
                    // echo 'changes found';

                    $update[$key] = $value;
                }
            }
        }

        return $update?? [];
    }
}
