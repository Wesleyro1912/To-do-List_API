<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Task as ModelsTask;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Validation;
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
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'GET') {
            try {
                // Busca de todas as tarefas
                $data = $this->taskModel->select('title, description, checked')->findAll();

                // Verifica se há tarefas retornadas
                if (empty($data)) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 'error',
                        'message' => 'Nenhuma tarefa encontrada.'
                    ]);
                    
                } else {
                    // Verifica se há tarefas com campos nulos ou vazios
                    foreach ($data as $task) {
                        if (is_null_or_empty($task->title) || is_null_or_empty($task->checked)) {
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
                }
        
            } catch (DatabaseException $e) {
                // Captura erros específicos do banco de dados e salva no log
                log_message('error', 'Erro de SELECT de TAREFAS no banco de dados: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao buscar tarefas no banco de dados.'
                ]);
                
            } catch (\Exception $e) {
                // Captura quaisquer outros erros e salva no log
                log_message('error', 'Erro inesperado de SELECT de TAREFAS: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Ocorreu um erro inesperado.'
                ]);
            }
            
        } else {
            // Método não permitido
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize GET para realizar as buscas.'
            ]);
        }  
    }


    // === Cadastro das tarefas ===
    public function store(){
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'POST') {
            
            // Obtem os dados da requisição 
            $title = $this->request->getJSON()->title;
            $description = $this->request->getJSON()->description;
            $checked = $this->request->getJSON()->checked;
            $created = new DateTime();
            $created_at = $created->format('Y-m-d H:i:s');

            // Transforma os dados em forma de Aray
            $taks = [
                'title' => $title,
                'description' => $description,
                'checked' => $checked,
                'created_at' => $created_at,
            ];

            // Verificação da validação
            if (!$this->validate(Validation::rulesStore(), Validation::messagesStore())) {
                // Log da falha de validação
                log_message('error', 'Erro na validação do CADASTRO de TAREFA: ' . implode(', ', $this->validator->getErrors()));
                
                // Resposta simplificada
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validação inválida',
                    'errors' => $this->validator->getErrors(),
                ]);
            } else {
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
                    // Erro de banco de dados
                    log_message('error', 'Erro no CADASTRO DE TAREFAS: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON([
                        'status' => 'error',
                        'message' => 'Não foi possível cadastrar a tarefa.'
                    ]);
                } catch (\Exception $e) {
                    // Erro genérico
                    log_message('error', 'Erro inesperado: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON([
                        'status' => 'error',
                        'message' => 'Erro inesperado.'
                    ]);
                }
            }

        // Método não permitido
        } else {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize POST para cadastrar.'
            ]);
        }  
    }

    
    // === Retorno de uma tarefa de acordo com o id para fazer o update/edição ===
    public function edit($id){
        
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'GET') {
            
            // Verificar se o ID é válido
            if (!isValidId($id)){
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido fornecido.'
                ]);
                
            }else {
                
                // Verifica se a tarefa existe
                if(!empty($this->taskModel->find($id))){
                    try {
                        
                        // Busca da tarefa de acordo com o id
                        $data = $this->taskModel->select('title, description, checked')->find($id);
        
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
                } else {
                    // Captura erros específicos do banco de dados e salva no log
                    log_message('error', 'Erro no banco de dados, tarefa não encontrada com o id: ' . $id);
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 'error',
                        'message' => 'Tarefa não encontrada'
                    ]);
                }
            }
                        
        // Método não permitido
        } else {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize GET para pesquisa.'
            ]);
        }
    }
    
    
    // === Edição de uma tarefa ===
    public function update($id) {
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'PUT') {
            
            // Verificar se o ID é válido
            if (!isValidId($id)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido fornecido.'
                ]);
            } else {
                
                // Verifica se a tarefa existe
                if (!empty($this->taskModel->find($id))) {
                
                    $title = $this->request->getJSON()->title;
                    $description = $this->request->getJSON()->description;
                    $checked = $this->request->getJSON()->checked;
                    $update = new DateTime();
                    $updated_at = $update->format('Y-m-d H:i:s');
        
                    // Prepara os dados para inserção em um array
                    $task = [
                        'title' => $title,
                        'description' => $description,
                        'checked' => $checked,
                        'updated_at' => $updated_at,
                    ];
                    
                    // Verificação da validação
                    if (!$this->validate(Validation::rulesUpdate(), Validation::messagesUpdate())) {
                        // Log da falha de validação
                        log_message('error', 'Erro na validação do CADASTRO de TAREFA: ' . implode(', ', $this->validator->getErrors()));
                        
                        // Resposta simplificada
                        return $this->response->setStatusCode(400)->setJSON([
                            'status' => 'error',
                            'message' => 'Validação inválida',
                            'errors' => $this->validator->getErrors(),
                        ]);
                    } else {
                        try {
                            // Atualização da tarefa com base no id
                            $data = $this->taskModel->update($id, $task);
                
                            // Retorna uma resposta JSON com as tarefas em caso de sucesso
                            return $this->response->setStatusCode(200)->setJSON([
                                'status' => 'success',
                                'message' => 'Tarefa atualizada com sucesso.',
                                'data' => $data,
                            ]);
                            
                        } catch (DatabaseException $e) {
                            // Captura erros específicos do banco de dados e salva no log
                            log_message('error', 'Erro de CADASTRO de TAREFA banco de dados: ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setJSON([
                                'status' => 'error',
                                'message' => 'Não foi possível cadastrar a tarefa.'
                            ]);
                        } catch (\Exception $e) {
                            // Captura quaisquer outros erros e salva no log
                            log_message('error', 'Erro inesperado de CADASTRO de TAREFA: ' . $e->getMessage());
                            return $this->response->setStatusCode(500)->setJSON([
                                'status' => 'error',
                                'message' => 'Erro inesperado.'
                            ]);
                        }
                    }
                } else {
                    // Tarefa não encontrada
                    log_message('error', 'Erro no banco de dados, tarefa não encontrada com o id: ' . $id);
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 'error',
                        'message' => 'Tarefa não encontrada'
                    ]);
                }
            }
        // Método não permitido
        } else {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize `PUT` para cadastrar.'
            ]);
        }
    }
    
    
    // === Delete de uma Tarefa ===
    public function delete($id) {
        
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'DELETE') {
            
            // Verificar se o ID é válido
            if (!isValidId($id)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido fornecido.'
                ]);
                
            } else {
                
                // Verifica se a tarefa existe
                if(!empty($this->taskModel->find($id))){
                    try {
                        
                        // Executa a exclusão do registro com o ID especificado
                        $this->taskModel->delete($id);

                        // Retorna uma resposta de sucesso
                        return $this->response->setStatusCode(200)->setJSON([
                            'status' => 'success',
                            'message' => 'Tarefa excluída com sucesso.'
                        ]);
                        
                    } catch (DatabaseException $e) {
                        
                        // Captura erros do banco de dados
                        log_message('error', 'Erro ao excluir tarefa: ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setJSON([
                            'status' => 'error',
                            'message' => 'Erro ao excluir tarefa do banco de dados, com id: '.$id,
                        ]);
                        
                    } catch (\Exception $e) {
                        
                        // Captura quaisquer outros erros e salva no log
                        log_message('error', 'Erro inesperado: ' . $e->getMessage());
                        return $this->response->setStatusCode(500)->setJSON([
                            'status' => 'error',
                            'message' => 'Erro inesperado.'
                        ]);
                    }
                } else {
                        // Captura erros específicos do banco de dados e salva no log
                        log_message('error', 'Erro no banco de dados, tarefa não encontrada com o id: ' . $id);
                        return $this->response->setStatusCode(404)->setJSON([
                            'status' => 'error',
                            'message' => 'Tarefa não encontrada'
                        ]);
                    }
            }
            
        
        } else {
            // Método não permitido
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize DELETE para deleta a tarefa.'
            ]);
        }
    }

        
    // === Altera o estado do campo checked === 
    public function status($id){
        
        // Verificar o método da requisição
        if ($this->request->getMethod() === 'PATCH') {
            
            // Verificar se o ID é válido
            if (!isValidId($id)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido fornecido.'
                ]);
                
            } else {
                try {
                    // Busca o valor atual de 'checked' da tarefa com o ID fornecido
                    $task = $this->taskModel->select('checked')->where('id', $id)->first();

                    // Verifica se a tarefa existe
                    if ($task) {
                        // Inverte o valor de 'checked' (0 para 1 e 1 para 0)
                        $newCheckedValue = $task->checked == 1 ? 0 : 1;

                        // Atualiza o valor de 'checked' no banco de dados
                        $this->taskModel->update($id, ['checked' => $newCheckedValue]);

                        // Retorna uma resposta de sucesso com o novo valor de 'checked'
                        return $this->response->setStatusCode(200)->setJSON([
                            'status' => 'success',
                            'message' => 'Status de checked atualizado com sucesso.',
                            'new_checked_value' => $newCheckedValue,
                        ]);
                        
                    } else {
                        // Tarefa não encontrada
                        return $this->response->setStatusCode(404)->setJSON([
                            'status' => 'error',
                            'message' => 'Tarefa não encontrada.'
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
        } else {
            // Método não permitido
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Método não permitido. Utilize PATCH para atualizar o status.'
            ]);
        }
    }


    

}