<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Car;
use App\Models\Order;

class AdminDashboardController extends Controller
{
    public function dashboardInfo(Request $request)
    {
        try {
            // Get total available quantity of cars
            $totalCarsQuantity = Car::sum('available_quantity');
            
            // Get total amount of orders
            $totalOrderAmount = Order::sum('total_amount');
            
            // Get total number of orders
            $totalOrdersCount = Order::count();
            
            // Construct the response
            $response = [
                'total_cars_quantity' => $totalCarsQuantity,
                'total_order_amount' => $totalOrderAmount,
                'total_orders_count' => $totalOrdersCount
            ];

            Log::info('Dashboard info fetched successfully');

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard info: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching dashboard info'], 500);
        }
    }

    public function addCars(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'brand' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'colors' => 'required|array', // colors should be an array
                'colors.*' => 'string|max:255', // each color should be a string
                'pictures' => 'required|array', // pictures should be an array
                'pictures.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // each picture should be an image file
                'year' => 'required|integer',
                'price' => 'required|numeric',
                'customs_price' => 'required|numeric',
                'available_quantity' => 'required|integer',
            ]);

            DB::beginTransaction();

            // Save uploaded images and get their paths
            $picturePaths = [];
            foreach ($request->file('pictures') as $picture) {
                $path = $picture->store('car_pictures');
                $picturePaths[] = $path;
            }

            // Create a new car instance with the validated data
            $car = new Car();
            $car->brand = $validatedData['brand'];
            $car->title = $validatedData['title'];
            $car->description = $validatedData['description'];
            $car->colors = implode(',', $validatedData['colors']); // Convert colors array to comma-separated string
            $car->pictures = json_encode($picturePaths); // Store picture paths as JSON
            $car->year = $validatedData['year'];
            $car->price = $validatedData['price'];
            $car->customs_price = $validatedData['customs_price'];
            $car->available_quantity = $validatedData['available_quantity'];

            // Save the car to the database
            $car->save();

            DB::commit();

            Log::info('Car added successfully');
            
            // Return a success response
            return response()->json(['message' => 'Car added successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error adding car: ' . $e->getMessage());
            return response()->json(['error' => 'Error adding car'], 500);
        }
    }
}