"use client";

import { useState, useEffect } from "react";
import { API_SERVICES, getAllApiRoutes, type ApiRouteConfig } from "@/lib/api-config";
import { getAllRouteMappings, getRouteMappingsByCategory, type RouteMapping } from "@/lib/route-mapper";

export default function SettingsPage() {
  const [apiRoutes, setApiRoutes] = useState<ApiRouteConfig[]>([]);
  const [routeMappings, setRouteMappings] = useState<RouteMapping[]>([]);
  const [routeCategories, setRouteCategories] = useState<Record<string, RouteMapping[]>>({});
  const [activeTab, setActiveTab] = useState<'routes' | 'mappings' | 'categories'>('routes');

  useEffect(() => {
    setApiRoutes(getAllApiRoutes());
    setRouteMappings(getAllRouteMappings());
    setRouteCategories(getRouteMappingsByCategory());
  }, []);

  return (
    <div className="max-w-6xl mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">API & Route Configuration</h1>
      
      <div className="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('routes')}
            className={`py-2 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'routes'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
            }`}
          >
            API Routes
          </button>
          <button
            onClick={() => setActiveTab('mappings')}
            className={`py-2 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'mappings'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
            }`}
          >
            Route Mappings
          </button>
          <button
            onClick={() => setActiveTab('categories')}
            className={`py-2 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'categories'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
            }`}
          >
            Categories
          </button>
        </nav>
      </div>

      {activeTab === 'routes' && (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <div className="px-4 py-5 sm:p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">API Routes Configuration</h2>
            
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead className="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Service
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Path
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Method
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Description
                    </th>
                    <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      Auth Required
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  {apiRoutes.map((route, index) => (
                    <tr key={index} className={index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'}>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {route.path.split('/')[2]?.toUpperCase() || 'GENERAL'}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {route.path}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-200 dark:text-blue-900">
                          {route.method || 'GET'}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                        {route.description}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {route.requiresAuth ? (
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900">
                            Yes
                          </span>
                        ) : (
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900">
                            No
                          </span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {activeTab === 'mappings' && (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <div className="px-4 py-5 sm:p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">UI to API Route Mappings</h2>
            
            <div className="space-y-4">
              {routeMappings.slice(0, 10).map((mapping, index) => (
                <div key={index} className="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                  <h3 className="font-medium text-gray-900 dark:text-white">{mapping.uiRoute}</h3>
                  <div className="mt-2 ml-4">
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">Connected API routes:</p>
                    <ul className="list-disc pl-5 space-y-1">
                      {mapping.apiRoutes.map((apiRoute, idx) => (
                        <li key={idx} className="text-sm text-gray-500 dark:text-gray-300">
                          {apiRoute.path} ({apiRoute.method || 'GET'}) - {apiRoute.description}
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              ))}
              
              {routeMappings.length > 10 && (
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-4">
                  Showing first 10 of {routeMappings.length} route mappings
                </p>
              )}
            </div>
          </div>
        </div>
      )}

      {activeTab === 'categories' && (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
          <div className="px-4 py-5 sm:p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Routes by Category</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {Object.entries(routeCategories).map(([category, mappings]) => (
                <div key={category} className="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                  <h3 className="font-medium text-gray-900 dark:text-white capitalize">{category}</h3>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">{mappings.length} routes</p>
                  <ul className="list-disc pl-5 space-y-1">
                    {mappings.slice(0, 5).map((mapping, idx) => (
                      <li key={idx} className="text-sm text-gray-500 dark:text-gray-300 truncate" title={mapping.uiRoute}>
                        {mapping.uiRoute}
                      </li>
                    ))}
                    {mappings.length > 5 && (
                      <li className="text-sm text-gray-500 dark:text-gray-400">
                        +{mappings.length - 5} more...
                      </li>
                    )}
                  </ul>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}