<?php $this->extends('master', ['title' => 'Home']); ?>

<h1 id="home">Olá <?php echo $name; ?></h1>

<?php echo $this->upper('alexandre'); ?>

<!-- In master template you must create a section with $this->section('menu') -->
<?php $this->start('menu'); ?>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/login">Login</a></li>
    </ul>
<?php $this->end(); ?>

<!-- In master template you must create a section with $this->section('css') -->
<?php $this->start('css'); ?>
    <style>
        h1#home{
            color:red;
        }
    </style>
<?php $this->end(); ?>

<ul>
    <?php foreach ($posts as $post): ?>
        <li><?php echo $post->title; ?></li>        
    <?php endforeach; ?>    
</ul>