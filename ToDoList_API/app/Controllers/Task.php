<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Task as ModelsTask;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Task extends BaseController{

    protected $taskModel;

    // === Inicializa variáveis, funções ou classes ===
    public function __construct(){
        // Inicializa a model/tabela tasks
        $this->taskModel = new ModelsTask();
        
        // Inicializa todos os helpers
        helper(['form', 'Task']);
        
    }    

    
    // === Retorno de todas as tarefas do banco de dados ===
    public function index() {
        try {
            // Busca de todas as tarefas
            $data = $this->taskModel->select('title, description, checked')->findAll();

            // Verifica se há tarefas retornadas
            if (empty($data)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Nenhuma tarefa encontrada.'
                ]);
            }

            // Verifica se as tarefas não contêm valores nulos ou vazios
            foreach ($data as $task) {
                if (is_null_or_empty($task['title']) || is_null_or_empty($task['checked'])) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status' => 'error',
                        'message' => 'Uma ou mais tarefas possuem valores nulos ou vazios.'
                    ]);
                }
            }
            
            // Retorno bem-sucedido
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data' => $data,
            ]);
            
        } catch (DatabaseException $e) {
            // Captura erros específicos do banco de dados e salva no log
            log_message('error', 'Erro de SELECT de TARREFAS no banco de dados: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Tarefas não foram encontradas.'
            ]);
            
        } catch (\Exception $e) {
            // Captura quaisquer outros erros e salva no log
            log_message('error', 'Erro inesperado de SELECT de TARREFAS: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Ocoreu um erro inesperado.'
            ]);
        }
    }


    // === Cadastro das tarefas ===
    public function store(){
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'post') {
            
            // Obtem os dados da requisição 
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $checked = false;
            $created = new DateTime();
            $created_at = $created->format('Y-m-d H:i:s');

            
            // Prepara os dados para inserção em um array
            $taks = [
                'title' => $title,
                'description' => $description,
                'checked' => $checked,
                'created_at' => $created_at,
            ];
            
            try {
                // Cadastrar a tarefa
                $data = $this->taskModel->insert($taks);
    
                // Retorna uma resposta JSON com as tarefas em caso de sucesso
                return $this->response->setStatusCode(201)->setJSON([
                    'status' => 'success',
                    'message' => 'Tarefa cadastrada com sucesso.',
                    'data' => $data
                ]);
                
            } catch (DatabaseException $e) {
                // Captura erros específicos do banco de dados e salva no log
                log_message('error', 'Erro de CADASTRO de TARREFA banco de dados: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Não foi possível cadastrar a tarefa.'
                ]);
                
            } catch (\Exception $e) {
                // Captura quaisquer outros erros e salva no log
                log_message('error', 'Erro inesperado de CADASTRO de TARREFA: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Erro inesperado.'
                ]);
            }
        
        }else{
            // Método não permitido
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize POST para cadastrar.'
            ]);
        }  
    }


    // === Retorno de uma tarefa de acordo com o id para fazer o update/edição ===
    public function edit($id){
        try {
            // Busca de todas as tarefas
            $data = $this->taskModel->select('title, description, checked')->find($id);

            // Verifica se há tarefas retornadas
            if (empty($data)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Nenhuma tarefa encontrada.'
                ]);
            }else{
                // Retorna uma resposta JSON com as tarefas em caso de sucesso
                return $this->response->setStatusCode(200)->setJSON([
                    'status' => 'success',
                    'data' => $data,
                ]);
            }

        } catch (DatabaseException $e) {
            // Captura erros específicos do banco de dados e salva no log
            log_message('error', 'Erro no banco de dados: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Erro no banco de dados.'
            ]);
            
        } catch (\Exception $e) {
            // Captura quaisquer outros erros e salva no log
            log_message('error', 'Erro inesperado: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Erro inesperado.'
            ]);
        }
    }
    
    
    // === Edição de uma tarefa ===
    public function update($id){
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'put') {
            
            // Obtem os dados da requisição PUT
            $input = $this->request->getRawInput();
            $title = $input['title'] ?? null;
            $description = $input['description'] ?? null;
            $checked = $input['checked'] ?? null;
            $update = new DateTime();
            $updated_at = $update->format('Y-m-d H:i:s');

            
            // Prepara os dados para inserção em um array
            $taks = [
                'title' => $title,
                'description' => $description,
                'checked' => $checked,
                'updated_at' => $updated_at,
            ];
            
            try {
                // Cadastrar a tarefa
                $data = $this->taskModel->update($id, $taks);
    
                // Retorna uma resposta JSON com as tarefas em caso de sucesso
                return $this->response->setStatusCode(201)->setJSON([
                    'status' => 'success',
                    'message' => 'Tarefa cadastrada com sucesso.',
                    'data' => $data,
                ]);
                
            } catch (DatabaseException $e) {
                // Captura erros específicos do banco de dados e salva no log
                log_message('error', 'Erro de CADASTRO de TARREFA banco de dados: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Não foi possível cadastrar a tarefa.'
                ]);
                
            } catch (\Exception $e) {
                // Captura quaisquer outros erros e salva no log
                log_message('error', 'Erro inesperado de CADASTRO de TARREFA: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Erro inesperado.'
                ]);
            }
        
        }else{
            // Método não permitido
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize POST para cadastrar.'
            ]);
        }  
    }

    
    // // === Delete de uma tarefa ===
    // public function delete($id){
    //     try {
    //         // Busca de todas as tarefas
    //         $data = $this->taskModel->select('title, description, checked')->findAll();

    //         // Retorna uma resposta JSON com as tarefas em caso de sucesso
    //         return $this->response->setStatusCode(200)->setJSON([
    //             'status' => 'success',
    //             'data' => $data,
    //         ]);
            
    //     } catch (DatabaseException $e) {
    //         // Captura erros específicos do banco de dados e salva no log
    //         log_message('error', 'Erro no banco de dados: ' . $e->getMessage());
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => 'Erro no banco de dados.'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         // Captura quaisquer outros erros e salva no log
    //         log_message('error', 'Erro inesperado: ' . $e->getMessage());
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => 'Erro inesperado.'
    //         ]);
    //     }
    // }

    public function delete($id) {
    try {
        // Verifica se o ID existe no banco de dados
        if ($this->taskModel->find($id)) {
            // Executa a exclusão do registro com o ID especificado
            $this->taskModel->delete($id);

            // Retorna uma resposta de sucesso
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'message' => 'Tarefa excluída com sucesso.'
            ]);
        } else {
            // Retorna uma resposta caso o ID não exista
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Tarefa não encontrada.'
            ]);
        }
    } catch (DatabaseException $e) {
        // Captura erros do banco de dados
        log_message('error', 'Erro ao excluir tarefa: ' . $e->getMessage());
        return $this->response->setStatusCode(500)->setJSON([
            'status' => 'error',
            'message' => 'Erro ao excluir tarefa do banco de dados.'
        ]);
    }
}

    
    // === Atualização do status de uma tarefa ===
    public function status($status){
        try {
            // Busca de todas as tarefas
            $data = $this->taskModel->select('title, description, checked')->findAll();

            // Retorna uma resposta JSON com as tarefas em caso de sucesso
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'data' => $data,
            ]);
            
        } catch (DatabaseException $e) {
            // Captura erros específicos do banco de dados e salva no log
            log_message('error', 'Erro no banco de dados: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Erro no banco de dados.'
            ]);
            
        } catch (\Exception $e) {
            // Captura quaisquer outros erros e salva no log
            log_message('error', 'Erro inesperado: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Erro inesperado.'
            ]);
        }
    }

}