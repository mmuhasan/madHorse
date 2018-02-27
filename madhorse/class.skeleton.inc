<?php

namespace madHorse;

class skeleton{
    protected $factory;
    protected $autoLoad;
    protected $config;
    protected $router;
    protected $request;
    protected $exceptionHandellar;
    
    public function __construct($objAutoLoad,$objFactory)
    { 
        $this->autoLoad =  $objAutoLoad;
        $this->factory  =  $objFactory;
    }
    
    public function growUp()
    {
        $this->config   = $this->loadConfig(); 

        //$this->logger      = $this->loadLogger();
        //$this->expHandeler = $this->loadExpHandeler();
        
        //$this->session     = $this->loadSession();        
        $this->request  = $this->loadRequest();
        //$this->uploadFiles = $this->loadUploadedFiles();    
        
        
        $this->router   = $this->loadRouter();
    }
  
    public function inSeason()
    {
        $this->component = $this->loadComponent ();
        $this->viewEngine= $this->loadViewEngine();
    }
    
    public function breed()
    {
        $this->component->run();
        $this->viewEngine->display($this->component);
    }
      
    private function loadComponent()
    {
        die(__LINE__dac);
        $objComponent = $this->factory->buildComponent($this->router);
        
        $objCompConfig= $this->loadComponentConfig($objComponent);
        $this->autoLoad->updateConfig($objCompConfig);
        $this->factory ->updateConfig($objCompConfig);
        
        $objCompRouter= $this->loadComponentRouter($strComponent);
        $this->router  ->updateRouter($objCompRouter);
        
        $objComponent->loadRouter ($this->router );
        $objComponent->loadFactory($this->factory);
        
        return $objComponent;
    }
      
    private function loadConfig()
    {
        $objConfig = $this->factory->build("config");
        $this->autoLoad->updateConfig($objConfig);
        $this->factory ->updateConfig($objConfig);
        
        return $objConfig;
    }
    
    private function loadRequest()
    {
        return $this->factory->buildRequest($this->getURL(),$_POST);
    }
    
    private function getURL()
    {
        return "";
    }
    
    private function loadRouter()
    {
        $objURL     = $this->request->getURL();
        return $this->factory->buildRouter($objURL);
    }
}