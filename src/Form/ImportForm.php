<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\Task1\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_import_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

  
    $form = array(
      '#attributes' => array('enctype' => 'multipart/form-data'),
    );
    

    $form['import_csv'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importcsv/',
      '#description' => t('CSV format only'),
      '#required' => TRUE,
      "#upload_validators"  => array("file_validate_extensions" => array("csv")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';


    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  
  public function submitForm(array &$form, FormStateInterface $form_state) {


    /* Fetch the array of the file stored temporarily in database */
    $csv_file = $form_state->getValue('import_csv');

    /* Load the object of the file by it's fid */
    $file = File::load( $csv_file[0] );
    
    /* Set the status flag permanent of the file object */
    $file->setPermanent();

    /* Save the file in database */
    $file->save();

    
    $data = $this->csvtoarray($file->getFileUri(), ',');
      // print_r($file, true);
      // die();


    
     foreach($data as $row) {
      $operations[] = ['\Drupal\Task1\addImportContent::addImportContentItem', [$row]];
    }

    $batch = array(
      'title' => t('Importing Data...'),
      'operations' => $operations,
      'init_message' => t('Creating Node.'),
      'finished' => '\Drupal\Task1\addImportContent::addImportContentItemCallback',
    );
    batch_set($batch);
  }

  public function csvtoarray($filename='', $delimiter){

    if(!file_exists($filename) || !is_readable($filename)) return FALSE;
    $header = NULL;
    $data = array();

    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
      {
        
        if(!$header){
          $header = $row;

        }else{
          $data[] = array_combine($header, $row);
          // print_r($row);
          // die();
        }
      }
      fclose($handle);
    }

    return $data;
  }

}

