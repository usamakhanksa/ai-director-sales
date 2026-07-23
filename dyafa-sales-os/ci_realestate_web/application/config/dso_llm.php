<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Dyafa Sales OS - LLM provider metadata and defaults.
 *
 * dso_llm_providers: known providers for the AiConfig "add provider" form.
 * adapter: which class in application/libraries/llm/ handles this provider.
 * 'openai_compatible' covers every provider whose HTTP API is a drop-in
 * match for OpenAI's /chat/completions request/response shape.
 */
$config['dso_llm_providers'] = array(
    'openai' => array(
        'label'        => 'OpenAI',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://api.openai.com/v1',
        'default_model'=> 'gpt-4o-mini',
        'needs_key'    => true,
        'free'         => false,
    ),
    'anthropic' => array(
        'label'        => 'Anthropic (Claude)',
        'adapter'      => 'anthropic',
        'base_url'     => 'https://api.anthropic.com',
        'default_model'=> 'claude-3-5-haiku-latest',
        'needs_key'    => true,
        'free'         => false,
    ),
    'gemini' => array(
        'label'        => 'Google Gemini',
        'adapter'      => 'gemini',
        'base_url'     => 'https://generativelanguage.googleapis.com/v1beta',
        'default_model'=> 'gemini-1.5-flash',
        'needs_key'    => true,
        'free'         => true,
    ),
    'groq' => array(
        'label'        => 'Groq',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://api.groq.com/openai/v1',
        'default_model'=> 'llama-3.1-8b-instant',
        'needs_key'    => true,
        'free'         => true,
    ),
    'openrouter' => array(
        'label'        => 'OpenRouter',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://openrouter.ai/api/v1',
        'default_model'=> 'meta-llama/llama-3.1-8b-instruct:free',
        'needs_key'    => true,
        'free'         => true,
    ),
    'ollama' => array(
        'label'        => 'Ollama (local)',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'http://localhost:11434/v1',
        'default_model'=> 'llama3.1',
        'needs_key'    => false,
        'free'         => true,
    ),
    'mistral' => array(
        'label'        => 'Mistral AI',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://api.mistral.ai/v1',
        'default_model'=> 'mistral-small-latest',
        'needs_key'    => true,
        'free'         => false,
    ),
    'deepseek' => array(
        'label'        => 'DeepSeek',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://api.deepseek.com/v1',
        'default_model'=> 'deepseek-chat',
        'needs_key'    => true,
        'free'         => false,
    ),
    'xai' => array(
        'label'        => 'xAI (Grok)',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://api.x.ai/v1',
        'default_model'=> 'grok-2-latest',
        'needs_key'    => true,
        'free'         => false,
    ),
    'azure_openai' => array(
        'label'        => 'Azure OpenAI',
        'adapter'      => 'openai_compatible',
        'base_url'     => 'https://YOUR-RESOURCE.openai.azure.com',
        'default_model'=> 'gpt-4o-mini',
        'needs_key'    => true,
        'free'         => false,
        'is_azure'     => true,
    ),
    'cohere' => array(
        'label'        => 'Cohere',
        'adapter'      => 'cohere',
        'base_url'     => 'https://api.cohere.com',
        'default_model'=> 'command-r',
        'needs_key'    => true,
        'free'         => true,
    ),
);

// Applied whenever a provider row's extra_params doesn't set its own value.
$config['dso_llm_defaults'] = array(
    'timeout'     => 8,   // seconds, curl connect+total timeout for a single completion call
    'temperature' => 0.3,
    'max_tokens'  => 300,
);
