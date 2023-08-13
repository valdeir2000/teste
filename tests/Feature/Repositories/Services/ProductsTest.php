<?php

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;

test('Um produto com dados inválidos não pode ser cadastrado', function () {
    $client = Mockery::mock(ProductRepository::class);
    $client->shouldReceive('create')->never();

    $service = new ProductService($client);
    $service->create([]);
})->throws(Exception::class);

test('O produto com dados válidos deve ser cadastrado', function () {
    $fake = Product::factory()->makeOne()->toArray();

    $service = new ProductService(new ProductRepository);
    $service->create($fake);

    $this->assertDatabaseHas('products', $fake);
});

test('Um produto inexistente não pode ser deletado', function () {
    $repository = Mockery::mock(ProductRepository::class);
    $repository->shouldReceive('deleteProduct')->never();

    $fake = Product::factory()->create();
    $fake->delete();
    $fake->refresh();
    $fake->fresh();

    $service = new ProductService($repository);
    $service->deleteProduct($fake);
})->throws(Exception::class);

test('Um produto válido pode ser deletado', function () {
    $repository = Mockery::mock(ProductRepository::class);
    $repository->shouldReceive('deleteProduct')->once();

    $fake = Product::factory()->create();

    $service = new ProductService($repository);
    $service->deleteProduct($fake);
});

test('Verifica se o produto retornado é igual ao informado através do ID', function () {
    $fake = Product::factory()->create();

    $service = new ProductService(new ProductRepository);
    $product = $service->getProductById($fake->id);

    $this->assertEquals($fake->toArray(), $product->toArray());
});

test('Ao capturar um produto inexistente, uma exceção deverá ser gerada', function () {
    $service = new ProductService(new ProductRepository);
    $service->getProductById(0);
})->throws(Exception::class);
