<?php
 namespace JocomRepo;

 interface CampaignInterface {

    public function getAll();

    public function fetchLatest();

    public function find(int $id);

    public function create(array $array);

    public function update(array $array, int $id);

 }