'use client';

import React from 'react';
import LinkedInProfileScraper from '../../../../../components/LinkedInProfileScraper';
import DashboardShell from '../../../../../components/DashboardShell';
import Link from 'next/link';

const LinkedInProfileScraperPage = () => {
  return (
    <DashboardShell>
      <div className="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900">LinkedIn Profile Scraper</h1>
          <p className="mt-2 text-lg text-gray-600">
            Extract detailed public profile information from LinkedIn profiles
          </p>
        </div>
        
        {/* Navigation Tabs */}
        <div className="border-b border-gray-200 mb-6">
          <nav className="-mb-px flex space-x-8">
            <Link
              href="/dashboard/tools/social/linkedin"
              className="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Profile Scraper
            </Link>
            <Link
              href="/dashboard/tools/social/linkedin/search"
              className="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
              Search Profiles
            </Link>
          </nav>
        </div>
        
        <div className="bg-white shadow rounded-lg p-6">
          <LinkedInProfileScraper />
        </div>
        
        <div className="mt-8 bg-blue-50 border-l-4 border-blue-400 p-4">
          <div className="flex">
            <div className="flex-shrink-0">
              <svg className="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
              </svg>
            </div>
            <div className="ml-3">
              <p className="text-sm text-blue-700">
                <strong>How to get your LinkedIn session cookie:</strong>
              </p>
              <ol className="mt-2 list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li>Login to your LinkedIn account in Chrome/Firefox</li>
                <li>Press F12 to open Developer Tools</li>
                <li>Go to the Application tab (Chrome) or Storage tab (Firefox)</li>
                <li>Under Cookies, select www.linkedin.com</li>
                <li>Find the cookie named "li_at"</li>
                <li>Copy the value and paste it in the form above</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </DashboardShell>
  );
};

export default LinkedInProfileScraperPage;